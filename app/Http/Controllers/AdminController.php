<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\IvfCycle;
use App\Models\Patient;
use App\Models\Setting;
use App\Support\AdminDemoData;
use Illuminate\Http\Request;

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
        return view('admin.login');
    }

    public function login(Request $request)
    {
        $email = strtolower((string) $request->input('email'));

        $role = 'admin';
        foreach (['doctor' => 'doctor', 'nurse' => 'nurse', 'reception' => 'receptionist', 'lab' => 'lab_technician'] as $needle => $r) {
            if (str_contains($email, $needle)) {
                $role = $r;
                break;
            }
        }

        $request->session()->put('admin_role', $role);

        return redirect('/admin');
    }

    public function logout(Request $request)
    {
        $request->session()->forget('admin_role');

        if ($request->query('reason') === 'timeout') {
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
            'reviews' => AdminDemoData::reviews(),
        ]);
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
            'branches' => AdminDemoData::branches(),
        ]);
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
            'messages' => AdminDemoData::messages(),
        ]);
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
