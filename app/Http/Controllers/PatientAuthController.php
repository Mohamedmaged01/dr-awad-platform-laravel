<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

/**
 * Real patient authentication for the patient portal (role = patient).
 */
class PatientAuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            throw ValidationException::withMessages(['portal' => __('invalidCredentials')]);
        }

        if (Auth::user()->role !== 'patient') {
            Auth::logout();
            throw ValidationException::withMessages(['portal' => __('invalidCredentials')]);
        }

        $request->session()->regenerate();

        return redirect('/patient-portal');
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:200'],
            'phone' => ['required', 'string', 'max:20'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6'],
            'file_number' => ['nullable', 'string', 'max:50'],
        ], [], ['email' => __('email')]);

        $parts = preg_split('/\s+/', trim($data['name']), 2);
        $fileNumber = $data['file_number'] ?? null;

        $user = DB::transaction(function () use ($data, $parts, $fileNumber) {
            $user = User::create([
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'role' => 'patient',
                'is_active' => true,
            ]);

            // Attach to an existing patient file (matched by phone) or create a new one.
            $patient = Patient::where('phone', $data['phone'])->whereNull('user_id')->first()
                ?? new Patient([
                    'file_number' => $fileNumber ?: ('P' . now()->format('Y') . str_pad((string) (Patient::count() + 1), 4, '0', STR_PAD_LEFT)),
                ]);

            $patient->fill([
                'user_id' => $user->id,
                'first_name_ar' => $parts[0] ?? $data['name'],
                'last_name_ar' => $parts[1] ?? '',
                'phone' => $data['phone'],
                'email' => $data['email'],
                'gender' => 'female',
            ]);
            $patient->medical_history = array_merge($patient->medical_history ?? [], ['status' => 'active', 'source' => 'portal']);
            $patient->save();

            return $user;
        });

        Auth::login($user);
        $request->session()->regenerate();

        return redirect('/patient-portal');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/patient-portal');
    }
}
