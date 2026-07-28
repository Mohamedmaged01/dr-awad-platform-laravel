<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Message;
use App\Models\Patient;
use App\Models\Subscriber;
use Illuminate\Http\Request;

/**
 * Persists the public-facing forms (booking, contact, newsletter).
 * Each returns to the page with a success flash the Blade view renders.
 */
class PublicFormController extends Controller
{
    public function submitBooking(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:200'],
            'phone' => ['required', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:255'],
            'branch' => ['required', 'exists:branches,id'],
            'service' => ['nullable', 'exists:services,id'],
            'date' => ['required', 'date', 'after_or_equal:today'],
            'time' => ['required', 'string', 'max:10'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $patient = $this->findOrCreatePatient($data['name'], $data['phone'], $data['email'] ?? null);

        Appointment::create([
            'patient_id' => $patient->id,
            'branch_id' => $data['branch'],
            'service_id' => $data['service'] ?? null,
            'appointment_date' => $data['date'],
            'appointment_time' => $data['time'],
            'status' => 'pending',
            'type' => 'online',
            'patient_notes' => $data['notes'] ?? null,
        ]);

        return back()->with('booking_success', true)->with('booking_summary', [
            'name' => $data['name'],
            'date' => $data['date'],
            'time' => $data['time'],
        ]);
    }

    public function submitContact(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'phone' => ['required', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:255'],
            'subject' => ['nullable', 'string', 'max:200'],
            'message' => ['required', 'string', 'max:2000'],
        ]);

        Message::create([
            'name' => $data['name'],
            'phone' => $data['phone'],
            'email' => $data['email'] ?? null,
            'subject' => $data['subject'] ?? null,
            'message' => $data['message'],
            'source' => 'contact_form',
            'status' => 'unread',
        ]);

        return back()->with('contact_success', true);
    }

    public function subscribe(Request $request)
    {
        $data = $request->validate([
            'email' => ['required', 'email', 'max:255'],
        ]);

        Subscriber::firstOrCreate(
            ['email' => $data['email']],
            ['is_active' => true, 'subscribed_at' => now()],
        );

        return back()->with('newsletter_success', true);
    }

    /** Match an existing patient by phone, else create a lightweight record. */
    private function findOrCreatePatient(string $name, string $phone, ?string $email): Patient
    {
        $patient = Patient::where('phone', $phone)->first();

        if ($patient) {
            return $patient;
        }

        $parts = preg_split('/\s+/', trim($name), 2);

        return Patient::create([
            'file_number' => 'P' . now()->format('Y') . str_pad((string) (Patient::count() + 1), 4, '0', STR_PAD_LEFT),
            'first_name_ar' => $parts[0] ?? $name,
            'last_name_ar' => $parts[1] ?? '',
            'phone' => $phone,
            'email' => $email,
            'gender' => 'female',
            'medical_history' => ['status' => 'active', 'source' => 'web'],
        ]);
    }
}
