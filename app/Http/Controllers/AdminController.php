<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Branch;
use App\Models\IvfCycle;
use App\Models\Message;
use App\Models\Patient;
use App\Models\Review;
use App\Models\Setting;
use App\Support\AdminDemoData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

/**
 * Admin dashboard. Demo login only (role inferred from email, no password check),
 * matching the Next.js prototype. Real records come from the seeded PostgreSQL
 * database where models exist; the remaining pages use demo arrays.
 */
class AdminController extends Controller
{
    /* ---------------------------------------------------------------- Auth (demo) */

    public function showLogin()
    {
        if (Auth::check() && Auth::user()->role !== 'patient') {
            return redirect('/admin');
        }

        return view('admin.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            throw ValidationException::withMessages(['email' => __('invalidCredentials')]);
        }

        $user = Auth::user();

        // The admin area is staff-only; a patient account cannot sign in here.
        if ($user->role === 'patient' || ! $user->is_active) {
            Auth::logout();
            throw ValidationException::withMessages(['email' => __('invalidCredentials')]);
        }

        $request->session()->regenerate();
        $user->forceFill(['last_login_at' => now()])->saveQuietly();

        return redirect()->intended('/admin');
    }

    public function logout(Request $request)
    {
        $timedOut = $request->query('reason') === 'timeout';

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if ($timedOut) {
            $request->session()->flash('timeout_message', __('sessionTimedOut'));
        }

        return redirect('/admin/login');
    }

    /* ---------------------------------------------------------------- Real pages */

    public function dashboard()
    {
        return view('admin.dashboard', [
            'stats' => Setting::json('dashboard_stats'),
            'recentAppointments' => Setting::json('dashboard_recent_appointments'),
            'ivfStats' => Setting::json('dashboard_ivf_stats'),
        ]);
    }

    public function patients()
    {
        return view('admin.patients', [
            'patients' => Patient::orderBy('file_number')->get(),
        ]);
    }

    public function appointments()
    {
        return view('admin.appointments', [
            'appointments' => Appointment::with(['patient', 'branch', 'service'])
                ->orderBy('appointment_time')
                ->get(),
            'stats' => Setting::json('appointments_stats'),
        ]);
    }

    public function ivf()
    {
        return view('admin.ivf', [
            'cycles' => IvfCycle::with(['patient', 'latestFollowup'])
                ->orderBy('cycle_number')
                ->orderBy('start_date')
                ->get(),
            'stats' => Setting::json('ivf_page_stats'),
            'tasks' => Setting::json('ivf_today_tasks'),
        ]);
    }

    public function updateAppointmentStatus(Request $request, Appointment $appointment)
    {
        $data = $request->validate([
            'status' => ['required', 'in:pending,confirmed,waiting,completed,cancelled,no_show'],
        ]);

        $appointment->update(['status' => $data['status']]);

        return back()->with('status', __('saved'));
    }

    public function destroyAppointment(Appointment $appointment)
    {
        $appointment->delete();

        return back()->with('status', __('deleted'));
    }

    public function destroyPatient(Patient $patient)
    {
        $patient->delete();

        return back()->with('status', __('deleted'));
    }

    public function permissions()
    {
        return view('admin.permissions');
    }

    /* ---------------------------------------------------------------- Demo pages */

    public function surgeries()
    {
        return view('admin.surgeries', [
            'surgeries' => AdminDemoData::surgeries(),
            'stats' => AdminDemoData::surgeryStats(),
        ]);
    }

    public function payments()
    {
        return view('admin.payments', [
            'invoices' => AdminDemoData::invoices(),
            'stats' => AdminDemoData::paymentStats(),
        ]);
    }

    public function reports()
    {
        return view('admin.reports', [
            'kpis' => AdminDemoData::reportKpis(),
            'monthly' => AdminDemoData::monthlyGrowth(),
            'distribution' => AdminDemoData::serviceDistribution(),
        ]);
    }

    public function reviews()
    {
        return view('admin.reviews', [
            'reviews' => Review::latest()->get()->map(fn (Review $r) => [
                'id' => $r->id,
                'patient' => $r->patient_name ?: $r->patient?->name,
                'rating' => $r->rating,
                'comment' => $r->content_ar,
                'service' => $r->title_ar,
                'status' => $r->is_approved ? 'approved' : 'pending',
            ])->all(),
        ]);
    }

    public function approveReview(Review $review)
    {
        $review->update(['is_approved' => true]);

        return back()->with('status', __('saved'));
    }

    public function destroyReview(Review $review)
    {
        $review->delete();

        return back()->with('status', __('deleted'));
    }

    public function staff()
    {
        return view('admin.staff', [
            'staff' => AdminDemoData::staff(),
        ]);
    }

    public function branches()
    {
        return view('admin.branches', [
            'branches' => Branch::orderByDesc('is_main')->get()->map(fn (Branch $b) => [
                'id' => $b->id,
                'name' => $b->name_ar,
                'address' => $b->address_ar,
                'phone' => $b->phone,
                'hours' => $b->working_hours['display'] ?? '',
            ])->all(),
        ]);
    }

    public function storeBranch(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:200'],
            'address' => ['required', 'string', 'max:500'],
            'phone' => ['required', 'string', 'max:20'],
            'hours' => ['nullable', 'string', 'max:100'],
        ]);

        Branch::create([
            'name_ar' => $data['name'],
            'address_ar' => $data['address'],
            'phone' => $data['phone'],
            'whatsapp' => $data['phone'],
            'working_hours' => ['display' => $data['hours'] ?? '', 'short' => $data['name']],
            'is_active' => true,
        ]);

        return back()->with('status', __('saved'));
    }

    public function updateBranch(Request $request, Branch $branch)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:200'],
            'address' => ['required', 'string', 'max:500'],
            'phone' => ['required', 'string', 'max:20'],
            'hours' => ['nullable', 'string', 'max:100'],
        ]);

        $branch->update([
            'name_ar' => $data['name'],
            'address_ar' => $data['address'],
            'phone' => $data['phone'],
            'working_hours' => array_merge($branch->working_hours ?? [], ['display' => $data['hours'] ?? '']),
        ]);

        return back()->with('status', __('saved'));
    }

    public function destroyBranch(Branch $branch)
    {
        $branch->delete();

        return back()->with('status', __('deleted'));
    }

    public function content()
    {
        return view('admin.content', [
            'content' => AdminDemoData::content(),
        ]);
    }

    public function messages()
    {
        return view('admin.messages', [
            'messages' => Message::latest()->get()->map(fn (Message $m) => [
                'id' => $m->id,
                'name' => $m->name,
                'email' => $m->email,
                'phone' => $m->phone,
                'subject' => $m->subject,
                'message' => $m->message,
                'time' => $m->created_at?->diffForHumans(),
                'unread' => $m->status === 'unread',
            ])->all(),
        ]);
    }

    public function replyMessage(Request $request, Message $message)
    {
        $data = $request->validate(['reply' => ['nullable', 'string', 'max:2000']]);

        $message->update([
            'reply' => $data['reply'] ?? null,
            'status' => 'replied',
            'replied_at' => now(),
        ]);

        return back()->with('status', __('saved'));
    }

    public function destroyMessage(Message $message)
    {
        $message->delete();

        return back()->with('status', __('deleted'));
    }

    public function settings()
    {
        return view('admin.settings');
    }

    /** Quick-action "/new" links the prototype never built a page for. */
    public function comingSoon()
    {
        $current = '/' . ltrim(request()->path(), '/');
        $key = collect(config('clinic.dashboard_menu'))->firstWhere('href', $current)['name'] ?? 'dashboard';

        return view('admin.coming-soon', ['pageTitle' => __($key)]);
    }
}
