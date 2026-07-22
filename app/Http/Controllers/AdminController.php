<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\IvfCycle;
use App\Models\Patient;
use App\Models\Setting;

/**
 * Read-only admin dashboard. Mirrors the prototype exactly; the demo records
 * come from the seeded PostgreSQL database, the stat blocks from `settings`.
 */
class AdminController extends Controller
{
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

    /** Sidebar links the prototype never built a page for. */
    public function comingSoon()
    {
        $current = '/' . ltrim(request()->path(), '/');
        $title = collect(config('clinic.dashboard_menu'))->firstWhere('href', $current)['name'] ?? 'قريباً';

        return view('admin.coming-soon', ['pageTitle' => $title]);
    }
}
