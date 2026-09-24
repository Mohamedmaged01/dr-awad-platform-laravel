<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Branch;
use App\Models\Invoice;
use App\Models\IvfCycle;
use App\Models\IvfFollowup;
use App\Models\Message;
use App\Models\Patient;
use App\Models\Payment;
use App\Models\Review;
use App\Models\Service;
use App\Models\Setting;
use App\Models\Staff;
use App\Models\Surgery;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

/**
 * Admin dashboard. Real authentication + role guards; every list and write
 * operation is backed by the PostgreSQL database (no demo/mock arrays).
 */
class AdminController extends Controller
{
    /* ---------------------------------------------------------------- Auth */

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

    /* ---------------------------------------------------------------- Dashboard & reports (live aggregates) */

    public function dashboard()
    {
        $revenue = (float) Payment::where('status', 'paid')->sum('amount');

        $stats = [
            ['title' => 'إجمالي المريضات', 'value' => number_format(Patient::count()), 'icon' => 'users', 'color' => 'bg-blue-500'],
            ['title' => 'مواعيد اليوم', 'value' => (string) Appointment::whereDate('appointment_date', today())->count(), 'icon' => 'calendar', 'color' => 'bg-green-500'],
            ['title' => 'إجمالي العمليات', 'value' => (string) Surgery::count(), 'icon' => 'stethoscope', 'color' => 'bg-purple-500'],
            ['title' => 'إجمالي الإيرادات', 'value' => number_format($revenue), 'icon' => 'credit-card', 'color' => 'bg-yellow-500', 'currency' => 'جنيه'],
        ];

        $recentAppointments = Appointment::with(['patient', 'service'])
            ->orderBy('appointment_time')
            ->take(6)
            ->get()
            ->map(fn (Appointment $a) => [
                'name' => $a->patient?->short_name ?? '',
                'service' => $a->service?->name_ar ?? '',
                'time' => $a->time_label,
                'status' => $a->status,
            ])->all();

        return view('admin.dashboard', [
            'stats' => $stats,
            'recentAppointments' => $recentAppointments,
            'ivfStats' => $this->ivfSummary(),
        ]);
    }

    /** IVF summary tiles, computed from the ivf_cycles table. */
    private function ivfSummary(): array
    {
        $completed = IvfCycle::where('current_stage', 'completed')->count();
        $pregnant = IvfCycle::where('is_pregnant', true)->count();

        return [
            ['label' => 'دورات نشطة', 'value' => (string) IvfCycle::where('current_stage', '!=', 'completed')->count(), 'icon' => 'activity', 'color' => 'text-blue-500'],
            ['label' => 'سحب بويضات', 'value' => (string) IvfCycle::where('current_stage', 'egg_retrieval')->count(), 'icon' => 'baby', 'color' => 'text-pink-500'],
            ['label' => 'زرع أجنة', 'value' => (string) IvfCycle::where('current_stage', 'embryo_transfer')->count(), 'icon' => 'trending-up', 'color' => 'text-green-500'],
            ['label' => 'نسبة النجاح', 'value' => $completed > 0 ? round($pregnant / $completed * 100) . '%' : '—', 'icon' => 'arrow-up-right', 'color' => 'text-purple-500'],
        ];
    }

    public function reports()
    {
        $revenue = (float) Payment::where('status', 'paid')->sum('amount');

        $kpis = [
            ['label' => 'إجمالي المريضات', 'value' => number_format(Patient::count()), 'icon' => 'users', 'color' => 'bg-blue-500'],
            ['label' => 'إجمالي المواعيد', 'value' => (string) Appointment::count(), 'icon' => 'calendar', 'color' => 'bg-green-500'],
            ['label' => 'العمليات', 'value' => (string) Surgery::count(), 'icon' => 'activity', 'color' => 'bg-purple-500'],
            ['label' => 'إجمالي الإيرادات', 'value' => number_format($revenue), 'icon' => 'dollar-sign', 'color' => 'bg-yellow-500'],
        ];

        // Appointments per calendar month (1-12) — grouped in PHP to stay portable
        // across MySQL/PostgreSQL (no vendor-specific date SQL).
        $monthly = array_fill(0, 12, 0);
        foreach (Appointment::get(['appointment_date']) as $appt) {
            if ($appt->appointment_date) {
                $monthly[(int) $appt->appointment_date->format('n') - 1]++;
            }
        }

        // Appointment distribution by service for the donut.
        $colors = ['bg-medical-blue', 'bg-light-gold', 'bg-pink-500', 'bg-emerald-500'];
        $grouped = Appointment::with('service')->get()
            ->groupBy(fn (Appointment $a) => $a->service?->name_ar ?? __('other'))
            ->map->count();
        $total = max($grouped->sum(), 1);
        $distribution = [];
        $i = 0;
        foreach ($grouped as $label => $count) {
            $distribution[] = ['label' => $label, 'percent' => (int) round($count / $total * 100), 'color' => $colors[$i % count($colors)]];
            $i++;
        }
        if (empty($distribution)) {
            $distribution[] = ['label' => __('noRecords'), 'percent' => 100, 'color' => 'bg-medical-blue'];
        }

        return view('admin.reports', [
            'kpis' => $kpis,
            'monthly' => $monthly,
            'distribution' => $distribution,
            'surgeryReport' => $this->surgeryReport(),
            'staffReport' => $this->staffReport(),
            'paymentReport' => $this->paymentReport(),
            'appointmentReport' => $this->appointmentReport(),
            'ivfReport' => $this->ivfReport(),
            'patientTypeReport' => $this->patientTypeReport(),
            'patientOptions' => $this->patientOptions(),
        ]);
    }

    /** Patients split by type — consultation (كشف), surgery, and IVF. */
    private function patientTypeReport(): array
    {
        return [
            ['label' => 'إجمالي المريضات', 'value' => number_format(Patient::count())],
            ['label' => 'مريضات الكشف', 'value' => number_format(Patient::whereHas('appointments')->count())],
            ['label' => 'مريضات العمليات', 'value' => number_format(Patient::whereHas('surgeries')->count())],
            ['label' => 'مريضات الحقن المجهري', 'value' => number_format(Patient::whereHas('ivfCycles')->count())],
        ];
    }

    /** Consultations (الكشف) report — cases by status. */
    private function appointmentReport(): array
    {
        $labels = ['pending' => 'معلق', 'confirmed' => 'مؤكد', 'waiting' => 'في الانتظار', 'completed' => 'مكتمل', 'cancelled' => 'ملغي', 'no_show' => 'لم يحضر'];
        $byStatus = Appointment::get(['status'])->groupBy('status')->map->count();
        $total = max(Appointment::count(), 1);

        return collect($labels)->map(fn ($lbl, $key) => [
            'label' => $lbl,
            'count' => (int) ($byStatus[$key] ?? 0),
            'percent' => (int) round(($byStatus[$key] ?? 0) / $total * 100),
        ])->values()->all();
    }

    /** IVF (الحقن المجهري) report — cycles by stage + success. */
    private function ivfReport(): array
    {
        $stageLabels = ['consultation' => 'استشارة', 'stimulation' => 'تنشيط', 'egg_retrieval' => 'سحب البويضات', 'fertilization' => 'التخصيب', 'embryo_transfer' => 'زرع الأجنة', 'pregnancy_test' => 'تحليل الحمل', 'completed' => 'مكتملة'];
        $byStage = IvfCycle::get(['current_stage'])->groupBy('current_stage')->map->count();
        $total = max(IvfCycle::count(), 1);
        $completed = IvfCycle::where('current_stage', 'completed')->count();
        $pregnant = IvfCycle::where('is_pregnant', true)->count();

        return [
            'kpis' => [
                ['label' => 'إجمالي الدورات', 'value' => number_format(IvfCycle::count())],
                ['label' => 'دورات نشطة', 'value' => number_format(IvfCycle::where('current_stage', '!=', 'completed')->count())],
                ['label' => 'حالات حمل', 'value' => number_format($pregnant)],
                ['label' => 'نسبة النجاح', 'value' => $completed > 0 ? round($pregnant / $completed * 100) . '%' : '—'],
            ],
            'stages' => collect($stageLabels)->map(fn ($lbl, $key) => [
                'label' => $lbl,
                'count' => (int) ($byStage[$key] ?? 0),
                'percent' => (int) round(($byStage[$key] ?? 0) / $total * 100),
            ])->values()->all(),
        ];
    }

    /** Surgeries report — totals, status split, and a breakdown by type. */
    private function surgeryReport(): array
    {
        $typeLabels = ['laparoscopy' => 'مناظير', 'hysteroscopy' => 'منظار رحمي', 'cesarean' => 'قيصرية', 'natural_delivery' => 'ولادة طبيعية', 'other' => 'أخرى'];
        $byType = Surgery::get(['surgery_type'])->groupBy('surgery_type')->map->count();
        $total = max(Surgery::count(), 1);

        return [
            'kpis' => [
                ['label' => 'إجمالي العمليات', 'value' => number_format(Surgery::count())],
                ['label' => 'مكتملة', 'value' => number_format(Surgery::where('status', 'completed')->count())],
                ['label' => 'مجدولة', 'value' => number_format(Surgery::where('status', 'scheduled')->count())],
                ['label' => 'إجمالي التكلفة', 'value' => number_format((float) Surgery::sum('total_cost')) . ' ج.م'],
            ],
            'types' => collect($typeLabels)->map(fn ($lbl, $k) => [
                'label' => $lbl,
                'count' => (int) ($byType[$k] ?? 0),
                'percent' => (int) round(($byType[$k] ?? 0) / $total * 100),
            ])->values()->all(),
        ];
    }

    /** Staff report — active team members per role (doctors, nurses, …). */
    private function staffReport(): array
    {
        $byRole = User::whereIn('role', config('clinic.staff_roles'))->where('is_active', true)
            ->get(['role'])->groupBy('role')->map->count();

        return collect(config('clinic.staff_roles'))->map(fn ($role) => [
            'label' => __(config("clinic.role_labels.$role")),
            'count' => (int) ($byRole[$role] ?? 0),
        ])->all();
    }

    /** Payments report — revenue totals and a split by payment method. */
    private function paymentReport(): array
    {
        $byMethod = Payment::where('status', 'paid')->get(['payment_method', 'amount'])
            ->groupBy(fn ($p) => $p->payment_method ?: 'أخرى')->map(fn ($g) => (float) $g->sum('amount'));

        return [
            'kpis' => [
                ['label' => 'إجمالي الإيرادات', 'value' => number_format((float) Payment::where('status', 'paid')->sum('amount')) . ' ج.م'],
                ['label' => 'مدفوعات هذا الشهر', 'value' => number_format((float) Payment::where('status', 'paid')->whereMonth('paid_at', now()->month)->whereYear('paid_at', now()->year)->sum('amount')) . ' ج.م'],
                ['label' => 'فواتير مدفوعة', 'value' => number_format(Invoice::where('status', 'paid')->count())],
                ['label' => 'فواتير معلقة', 'value' => number_format(Invoice::where('status', 'pending')->count())],
            ],
            'methods' => $byMethod->map(fn ($amount, $method) => ['label' => $method, 'amount' => $amount])->values()->all(),
        ];
    }

    /**
     * Export a detailed report as a CSV (opens in Excel), optionally limited to a
     * date range. `type` selects the dataset: patients | staff | surgeries | payments.
     */
    public function exportReport(Request $request)
    {
        [$type, $from, $to, $patientId] = $this->reportParams($request);
        [$name, , $headers, $rows] = $this->reportData($type, $from, $to, $patientId);

        $suffix = ($from ? $from->format('Ymd') : 'all') . '-' . ($to ? $to->format('Ymd') : 'all');
        $filename = "report-{$name}-{$suffix}.csv";

        return response()->streamDownload(function () use ($headers, $rows) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF"); // BOM for Arabic in Excel
            fputcsv($out, $headers);
            foreach ($rows as $r) {
                fputcsv($out, $r);
            }
            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    /** Printable report (official header + logo); staff use the browser to save as PDF. */
    public function printReport(Request $request)
    {
        [$type, $from, $to, $patientId] = $this->reportParams($request);
        [, $title, $headers, $rows] = $this->reportData($type, $from, $to, $patientId);
        $patient = $patientId ? Patient::withTrashed()->find($patientId) : null;

        return view('admin.report-print', compact('title', 'headers', 'rows', 'from', 'to', 'patient'));
    }

    /** Shared query params for both export + print. */
    private function reportParams(Request $request): array
    {
        return [
            $request->query('type', 'patients'),
            $request->query('from') ? Carbon::parse($request->query('from'))->startOfDay() : null,
            $request->query('to') ? Carbon::parse($request->query('to'))->endOfDay() : null,
            $request->query('patient_id') ?: null,
        ];
    }

    /** [machine-name, title, headers[], rows[]] for a report type. */
    private function reportData(string $type, ?Carbon $from, ?Carbon $to, ?string $patientId): array
    {
        return match ($type) {
            'staff' => $this->reportStaffRows($from, $to),
            'users' => $this->reportUsersRows(),
            'surgeries' => $this->reportSurgeryRows($from, $to, $patientId),
            'payments' => $this->reportPaymentRows($from, $to, $patientId),
            'appointments' => $this->reportAppointmentRows($from, $to, $patientId),
            'ivf' => $this->reportIvfRows($from, $to, $patientId),
            'patients_check' => $this->reportCheckPatientRows($from, $to),
            'patients_surgery' => $this->reportSurgeryPatientRows($from, $to),
            default => $this->reportPatientRows($from, $to, $patientId),
        };
    }

    /** System users + their role and the features their role can access. */
    private function reportUsersRows(): array
    {
        $roleLabels = config('clinic.role_labels');
        $rows = User::with('staff')->whereIn('role', config('clinic.staff_roles'))->orderBy('role')->get()
            ->map(function (User $u) use ($roleLabels) {
                $features = collect(\App\Support\Access::allowedMenu($u->role))->map(fn ($i) => __($i['name']))->implode('، ');

                return [
                    $u->staff?->name ?: '—',
                    $u->email,
                    isset($roleLabels[$u->role]) ? __($roleLabels[$u->role]) : $u->role,
                    $u->is_active ? 'نشط' : 'موقوف',
                    $features,
                ];
            })->all();

        return ['users', 'تقرير مستخدمي النظام والصلاحيات', ['الاسم', 'البريد الإلكتروني', 'الدور', 'الحالة', 'الصلاحيات'], $rows];
    }

    private function reportPatientRows(?Carbon $from, ?Carbon $to, ?string $patientId = null): array
    {
        $q = Patient::withTrashed()->orderByDesc('created_at');
        $patientId && $q->where('id', $patientId);
        $from && $q->where('created_at', '>=', $from);
        $to && $q->where('created_at', '<=', $to);

        $rows = $q->get()->map(fn (Patient $p) => [
            $p->file_number, $p->name, $p->phone, $p->email, $p->age, $p->case_type,
            $p->last_visit, $p->demo_status === 'active' ? 'نشط' : 'مؤرشف',
            optional($p->created_at)->format('Y-m-d H:i'),
        ])->all();

        return ['patients', 'تقرير المريضات', ['رقم الملف', 'الاسم', 'الهاتف', 'البريد الإلكتروني', 'العمر', 'نوع الحالة', 'آخر زيارة', 'الحالة', 'تاريخ الإضافة'], $rows];
    }

    private function reportStaffRows(?Carbon $from, ?Carbon $to): array
    {
        $labels = config('clinic.role_labels');
        $q = Staff::with('user')->orderByDesc('created_at');
        $from && $q->where('created_at', '>=', $from);
        $to && $q->where('created_at', '<=', $to);

        $rows = $q->get()->map(fn (Staff $s) => [
            $s->name,
            isset($labels[$s->user?->role]) ? __($labels[$s->user->role]) : ($s->user?->role ?? ''),
            $s->title, $s->phone, $s->user?->email,
            $s->is_available ? 'متاح' : 'غير متاح',
            optional($s->created_at)->format('Y-m-d H:i'),
        ])->all();

        return ['staff', 'تقرير الفريق الطبي', ['الاسم', 'الدور', 'المسمى الوظيفي', 'الهاتف', 'البريد الإلكتروني', 'الحالة', 'تاريخ الإضافة'], $rows];
    }

    private function reportSurgeryRows(?Carbon $from, ?Carbon $to, ?string $patientId = null): array
    {
        $typeLabels = ['laparoscopy' => 'مناظير', 'hysteroscopy' => 'منظار رحمي', 'cesarean' => 'قيصرية', 'natural_delivery' => 'ولادة طبيعية', 'other' => 'أخرى'];
        $statusLabels = ['scheduled' => 'مجدولة', 'pending' => 'معلقة', 'completed' => 'مكتملة', 'cancelled' => 'ملغية'];
        $q = Surgery::with(['patient' => fn ($x) => $x->withTrashed(), 'staff' => fn ($x) => $x->withTrashed()])->orderByDesc('scheduled_date');
        $patientId && $q->where('patient_id', $patientId);
        $from && $q->where('scheduled_date', '>=', $from);
        $to && $q->where('scheduled_date', '<=', $to);

        $rows = $q->get()->map(fn (Surgery $s) => [
            $s->patient?->name ?? '—', $s->patient?->file_number ?? '',
            $s->surgery_name, $typeLabels[$s->surgery_type] ?? $s->surgery_type,
            optional($s->scheduled_date)->format('Y-m-d'), optional($s->scheduled_date)->format('H:i'),
            $s->staff?->name ?? 'د. محمد عوض', number_format((float) $s->total_cost),
            $statusLabels[$s->status] ?? $s->status,
        ])->all();

        return ['surgeries', 'تقرير العمليات', ['المريضة', 'رقم الملف', 'العملية', 'النوع', 'التاريخ', 'الوقت', 'الطبيب', 'التكلفة', 'الحالة'], $rows];
    }

    /** Consultation patients — those with appointments (optionally within a range). */
    private function reportCheckPatientRows(?Carbon $from, ?Carbon $to): array
    {
        $dateFilter = function ($q) use ($from, $to) {
            $from && $q->where('appointment_date', '>=', $from);
            $to && $q->where('appointment_date', '<=', $to);
        };

        $rows = Patient::whereHas('appointments', $dateFilter)
            ->withCount(['appointments as visits' => $dateFilter])
            ->orderByDesc('created_at')->get()
            ->map(fn (Patient $p) => [
                $p->file_number, $p->name, $p->phone, $p->age, $p->case_type, $p->visits, $p->last_visit,
            ])->all();

        return ['patients-check', 'تقرير مريضات الكشف', ['رقم الملف', 'الاسم', 'الهاتف', 'العمر', 'نوع الحالة', 'عدد الكشوفات', 'آخر زيارة'], $rows];
    }

    /** Surgery patients — those with surgeries (optionally within a range). */
    private function reportSurgeryPatientRows(?Carbon $from, ?Carbon $to): array
    {
        $dateFilter = function ($q) use ($from, $to) {
            $from && $q->where('scheduled_date', '>=', $from);
            $to && $q->where('scheduled_date', '<=', $to);
        };

        $rows = Patient::whereHas('surgeries', $dateFilter)
            ->withCount(['surgeries as ops' => $dateFilter])
            ->withSum(['surgeries as ops_cost' => $dateFilter], 'total_cost')
            ->orderByDesc('created_at')->get()
            ->map(fn (Patient $p) => [
                $p->file_number, $p->name, $p->phone, $p->age, $p->ops, number_format((float) $p->ops_cost),
            ])->all();

        return ['patients-surgery', 'تقرير مريضات العمليات', ['رقم الملف', 'الاسم', 'الهاتف', 'العمر', 'عدد العمليات', 'إجمالي التكلفة'], $rows];
    }

    private function reportAppointmentRows(?Carbon $from, ?Carbon $to, ?string $patientId = null): array
    {
        $statusLabels = ['pending' => 'معلق', 'confirmed' => 'مؤكد', 'waiting' => 'في الانتظار', 'completed' => 'مكتمل', 'cancelled' => 'ملغي', 'no_show' => 'لم يحضر'];
        $q = Appointment::with([
            'patient' => fn ($x) => $x->withTrashed(),
            'branch' => fn ($x) => $x->withTrashed(),
            'service' => fn ($x) => $x->withTrashed(),
        ])->orderByDesc('appointment_date');
        $patientId && $q->where('patient_id', $patientId);
        $from && $q->where('appointment_date', '>=', $from);
        $to && $q->where('appointment_date', '<=', $to);

        $rows = $q->get()->map(fn (Appointment $a) => [
            $a->patient?->name ?? '—', $a->patient?->phone ?? '',
            $a->branch?->name_ar ?? '', $a->service?->name_ar ?? '',
            optional($a->appointment_date)->format('Y-m-d'), $a->time_label,
            $statusLabels[$a->status] ?? $a->status,
            $a->notes ?: $a->patient_notes,
        ])->all();

        return ['appointments', 'تقرير الكشوفات', ['المريضة', 'الهاتف', 'الفرع', 'الخدمة', 'التاريخ', 'الوقت', 'الحالة', 'ملاحظات'], $rows];
    }

    private function reportIvfRows(?Carbon $from, ?Carbon $to, ?string $patientId = null): array
    {
        $stageLabels = ['consultation' => 'استشارة', 'stimulation' => 'تنشيط', 'egg_retrieval' => 'سحب البويضات', 'fertilization' => 'التخصيب', 'embryo_transfer' => 'زرع الأجنة', 'pregnancy_test' => 'تحليل الحمل', 'completed' => 'مكتملة'];
        $q = IvfCycle::with(['patient' => fn ($x) => $x->withTrashed(), 'latestFollowup'])->orderByDesc('start_date');
        $patientId && $q->where('patient_id', $patientId);
        $from && $q->where('start_date', '>=', $from);
        $to && $q->where('start_date', '<=', $to);

        $doseLabels = ['normal' => 'عادية', 'full' => 'كاملة'];
        $rows = $q->get()->map(fn (IvfCycle $c) => [
            $c->patient?->name ?? '—',
            $c->cycle_number, strtoupper((string) $c->cycle_type), $c->protocol,
            $doseLabels[$c->dose] ?? $c->dose,
            $stageLabels[$c->current_stage] ?? $c->current_stage,
            optional($c->start_date)->format('Y-m-d'),
            optional($c->stimulation_start_date)->format('Y-m-d'),
            optional($c->stimulation_end_date)->format('Y-m-d'),
            optional($c->egg_retrieval_date)->format('Y-m-d'),
            optional($c->fertilization_date)->format('Y-m-d'),
            optional($c->embryo_transfer_date)->format('Y-m-d'),
            $c->freezing_note,
            $c->is_pregnant === null ? '—' : ($c->is_pregnant ? 'إيجابية' : 'سلبية'),
        ])->all();

        return ['ivf', 'تقرير الحقن المجهري', ['المريضة', 'رقم الدورة', 'النوع', 'البروتوكول', 'الجرعة', 'المرحلة', 'تاريخ البدء', 'بداية التنشيط', 'نهاية التنشيط', 'سحب البويضات', 'التخصيب', 'إرجاع الأجنة', 'تجميد', 'النتيجة'], $rows];
    }

    private function reportPaymentRows(?Carbon $from, ?Carbon $to, ?string $patientId = null): array
    {
        $q = Payment::with('invoice.patient')->orderByDesc('paid_at');
        $patientId && $q->whereHas('invoice', fn ($x) => $x->where('patient_id', $patientId));
        $from && $q->where('paid_at', '>=', $from);
        $to && $q->where('paid_at', '<=', $to);

        $rows = $q->get()->map(fn (Payment $p) => [
            $p->invoice?->invoice_number ?? '', $p->invoice?->patient?->name ?? '—',
            number_format((float) $p->amount), $p->payment_method,
            $p->status === 'paid' ? 'مدفوع' : $p->status,
            optional($p->paid_at)->format('Y-m-d H:i'),
        ])->all();

        return ['payments', 'تقرير المدفوعات', ['رقم الفاتورة', 'المريضة', 'المبلغ', 'طريقة الدفع', 'الحالة', 'تاريخ الدفع'], $rows];
    }

    /* ---------------------------------------------------------------- Patients */

    public function patients()
    {
        return view('admin.patients', [
            // Newest patients first. id is a time-ordered UUID (v7), so it is a
            // stable tiebreak that also reflects creation order for same-timestamp rows.
            'patients' => Patient::orderByDesc('created_at')->orderByDesc('id')->get(),
        ]);
    }

    /** Download the patients list as a CSV (opens in Excel; UTF-8 BOM for Arabic). */
    public function exportPatients()
    {
        $patients = Patient::orderByDesc('created_at')->orderByDesc('id')->get();
        $filename = 'patients-' . now()->format('Y-m-d') . '.csv';

        return response()->streamDownload(function () use ($patients) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF"); // BOM so Excel renders Arabic correctly
            fputcsv($out, ['رقم الملف', 'الاسم', 'الهاتف', 'البريد الإلكتروني', 'العمر', 'نوع الحالة', 'آخر زيارة', 'الحالة']);
            foreach ($patients as $p) {
                fputcsv($out, [
                    $p->file_number,
                    $p->name,
                    $p->phone,
                    $p->email,
                    $p->age,
                    $p->case_type,
                    $p->last_visit,
                    $p->demo_status === 'active' ? 'نشط' : 'مؤرشف',
                ]);
            }
            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    public function storePatient(Request $request)
    {
        $data = $request->validate([
            'first_name_ar' => ['required', 'string', 'max:100'],
            'last_name_ar' => ['required', 'string', 'max:100'],
            'phone' => ['required', 'string', 'max:20'],
            'phone_alt' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:255'],
            'date_of_birth' => ['nullable', 'date'],
            'national_id' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:500'],
            'blood_type' => ['nullable', 'string', 'max:5'],
            'emergency_contact' => ['nullable', 'string', 'max:100'],
            'case_type' => ['nullable', 'string', 'max:100'],
        ]);

        Patient::create([
            'file_number' => $this->generateFileNumber(),
            'first_name_ar' => $data['first_name_ar'],
            'last_name_ar' => $data['last_name_ar'],
            'phone' => $data['phone'],
            'phone_alt' => $data['phone_alt'] ?? null,
            'email' => $data['email'] ?? null,
            'date_of_birth' => $data['date_of_birth'] ?? null,
            'national_id' => $data['national_id'] ?? null,
            'address' => $data['address'] ?? null,
            'blood_type' => $data['blood_type'] ?? null,
            'emergency_contact' => $data['emergency_contact'] ?? null,
            'gender' => 'female',
            'medical_history' => [
                'type' => $data['case_type'] ?? 'كشف طبي',
                'last_visit' => today()->toDateString(),
                'status' => 'active',
                'age' => isset($data['date_of_birth']) ? Carbon::parse($data['date_of_birth'])->age : null,
                'short_name' => trim($data['first_name_ar'] . ' ' . $data['last_name_ar']),
            ],
        ]);

        return back()->with('status', __('saved'));
    }

    public function updatePatient(Request $request, Patient $patient)
    {
        $data = $request->validate([
            'first_name_ar' => ['required', 'string', 'max:100'],
            'last_name_ar' => ['required', 'string', 'max:100'],
            'phone' => ['required', 'string', 'max:20'],
            'phone_alt' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:255'],
            'address' => ['nullable', 'string', 'max:500'],
            'case_type' => ['nullable', 'string', 'max:100'],
            'demo_status' => ['nullable', 'in:active,archived'],
            'password' => ['nullable', 'string', 'min:6'],
        ]);

        $history = $patient->medical_history ?? [];
        $history['short_name'] = trim($data['first_name_ar'] . ' ' . $data['last_name_ar']);
        if (array_key_exists('case_type', $data) && $data['case_type']) {
            $history['type'] = $data['case_type'];
        }
        if (! empty($data['demo_status'])) {
            $history['status'] = $data['demo_status'];
        }

        $patient->update([
            'first_name_ar' => $data['first_name_ar'],
            'last_name_ar' => $data['last_name_ar'],
            'phone' => $data['phone'],
            'phone_alt' => $data['phone_alt'] ?? $patient->phone_alt,
            'email' => $data['email'] ?? $patient->email,
            'address' => $data['address'] ?? $patient->address,
            'medical_history' => $history,
        ]);

        // Admin sets/resets the patient's portal login password from the editor.
        if (! empty($data['password'])) {
            $this->setPatientPassword($patient, $data['password']);
        }

        return back()->with('status', __('saved'));
    }

    /** Quick password change from the patients list (dedicated key-icon dialog). */
    public function updatePatientPassword(Request $request, Patient $patient)
    {
        $data = $request->validate([
            'password' => ['required', 'string', 'min:6'],
        ]);

        $this->setPatientPassword($patient, $data['password']);

        return back()->with('status', __('passwordChanged'));
    }

    /**
     * Set/reset a patient's portal login password. If the patient has no login
     * yet, one is created when a free email is available; otherwise it errors.
     */
    private function setPatientPassword(Patient $patient, string $password): void
    {
        if ($patient->user) {
            $patient->user->update(['password' => Hash::make($password)]);
        } elseif (! empty($patient->email) && ! User::where('email', $patient->email)->exists()) {
            $user = User::create([
                'email' => $patient->email,
                'password' => Hash::make($password),
                'role' => 'patient',
                'is_active' => true,
            ]);
            $patient->update(['user_id' => $user->id]);
        } else {
            throw ValidationException::withMessages(['password' => __('cannotSetPatientPassword')]);
        }
    }

    public function destroyPatient(Patient $patient)
    {
        $patient->delete();

        return back()->with('status', __('deleted'));
    }

    /* ---------------------------------------------------------------- Appointments */

    public function appointments()
    {
        return view('admin.appointments', [
            // Load related records including soft-deleted ones, so an appointment
            // whose patient/branch/service was later deleted still renders (and
            // never crashes the page on a null relation).
            'appointments' => Appointment::with([
                'patient' => fn ($q) => $q->withTrashed(),
                'branch' => fn ($q) => $q->withTrashed(),
                'service' => fn ($q) => $q->withTrashed(),
            ])
                ->orderBy('appointment_time')
                ->get(),
            'stats' => $this->appointmentStats(),
            'patientOptions' => $this->patientOptions(),
            'branchOptions' => $this->branchOptions(),
            'serviceOptions' => $this->serviceOptions(),
        ]);
    }

    private function appointmentStats(): array
    {
        return [
            ['label' => 'إجمالي المواعيد', 'value' => Appointment::count(), 'color' => 'bg-blue-500'],
            ['label' => 'مؤكدة', 'value' => Appointment::where('status', 'confirmed')->count(), 'color' => 'bg-green-500'],
            ['label' => 'في الانتظار', 'value' => Appointment::whereIn('status', ['waiting', 'pending'])->count(), 'color' => 'bg-yellow-500'],
            ['label' => 'ملغية', 'value' => Appointment::where('status', 'cancelled')->count(), 'color' => 'bg-red-500'],
        ];
    }

    public function storeAppointment(Request $request)
    {
        $data = $request->validate([
            'patient_id' => ['required', 'exists:patients,id'],
            'branch_id' => ['required', 'exists:branches,id'],
            'service_id' => ['nullable', 'exists:services,id'],
            'appointment_date' => ['required', 'date'],
            'appointment_time' => ['nullable'],
            'status' => ['required', 'in:pending,confirmed,waiting,completed,cancelled,no_show'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        // Time is optional — the clinic sets it when confirming; empty means unset.
        $data['appointment_time'] = ($data['appointment_time'] ?? null) ?: null;

        Appointment::create($data + ['type' => 'clinic']);

        return back()->with('status', __('saved'));
    }

    public function updateAppointment(Request $request, Appointment $appointment)
    {
        $data = $request->validate([
            'branch_id' => ['required', 'exists:branches,id'],
            'service_id' => ['nullable', 'exists:services,id'],
            'appointment_date' => ['required', 'date'],
            'appointment_time' => ['nullable'],
            'status' => ['required', 'in:pending,confirmed,waiting,completed,cancelled,no_show'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        // Empty time clears it (kept unset until the clinic schedules it).
        $data['appointment_time'] = ($data['appointment_time'] ?? null) ?: null;

        $appointment->update($data);

        return back()->with('status', __('saved'));
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

    /* ---------------------------------------------------------------- IVF */

    public function ivf()
    {
        return view('admin.ivf', [
            'cycles' => IvfCycle::with(['patient' => fn ($q) => $q->withTrashed(), 'latestFollowup'])
                ->orderBy('cycle_number')
                ->orderBy('start_date')
                ->get(),
            'stats' => $this->ivfPageStats(),
            'tasks' => Setting::json('ivf_today_tasks'),
            'patientOptions' => $this->patientOptions(),
            'patientData' => $this->patientContactMap(),
        ]);
    }

    /** id → {age, phone, address} so the new-cycle form can prefill patient fields. */
    private function patientContactMap(): array
    {
        return Patient::orderBy('file_number')->get()
            ->mapWithKeys(fn (Patient $p) => [$p->id => [
                'age' => $p->age,
                'phone' => $p->phone,
                'address' => $p->address,
            ]])->all();
    }

    private function ivfPageStats(): array
    {
        $completed = IvfCycle::where('current_stage', 'completed')->count();
        $pregnant = IvfCycle::where('is_pregnant', true)->count();

        return [
            ['label' => 'دورات نشطة', 'value' => (string) IvfCycle::where('current_stage', '!=', 'completed')->count(), 'icon' => 'activity', 'color' => 'bg-blue-500'],
            ['label' => 'سحب بويضات', 'value' => (string) IvfCycle::where('current_stage', 'egg_retrieval')->count(), 'icon' => 'flask-conical', 'color' => 'bg-purple-500'],
            ['label' => 'زرع أجنة', 'value' => (string) IvfCycle::where('current_stage', 'embryo_transfer')->count(), 'icon' => 'baby', 'color' => 'bg-pink-500'],
            ['label' => 'نسبة النجاح', 'value' => $completed > 0 ? round($pregnant / $completed * 100) . '%' : '—', 'icon' => 'trending-up', 'color' => 'bg-green-500'],
        ];
    }

    public function storeCycle(Request $request)
    {
        $data = $this->validateCycle($request);
        $this->syncCyclePatient($data);

        $number = IvfCycle::where('patient_id', $data['patient_id'])->max('cycle_number') + 1;

        $cycle = IvfCycle::create($this->cycleAttributes($data) + [
            'staff_id' => $this->doctorStaffId(),
            'cycle_number' => $number,
        ]);

        IvfFollowup::create([
            'cycle_id' => $cycle->id,
            'followup_date' => $data['start_date'],
            'day_of_cycle' => 1,
            'next_appointment' => $data['egg_retrieval_date'] ?? ($data['embryo_transfer_date'] ?? null),
        ]);

        return back()->with('status', __('saved'));
    }

    /** Edit an existing IVF cycle (view/edit icons on the cycle cards). */
    public function updateCycle(Request $request, IvfCycle $cycle)
    {
        $data = $this->validateCycle($request);
        $this->syncCyclePatient($data);
        $cycle->update($this->cycleAttributes($data));

        return back()->with('status', __('saved'));
    }

    private function validateCycle(Request $request): array
    {
        return $request->validate([
            'patient_id' => ['required', 'exists:patients,id'],
            'age' => ['nullable', 'integer', 'min:0', 'max:120'],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:500'],
            'cycle_type' => ['required', 'string', 'max:50'],
            'protocol' => ['required', 'string', 'max:100'],
            'dose' => ['nullable', 'in:normal,full'],
            'start_date' => ['required', 'date'],
            'current_stage' => ['required', 'in:consultation,stimulation,egg_retrieval,fertilization,embryo_transfer,pregnancy_test,completed'],
            'stimulation_start_date' => ['nullable', 'date'],
            'stimulation_end_date' => ['nullable', 'date'],
            'egg_retrieval_date' => ['nullable', 'date'],
            'fertilization_date' => ['nullable', 'date'],
            'embryo_transfer_date' => ['nullable', 'date'],
            'freezing_note' => ['nullable', 'string', 'max:500'],
            'final_result' => ['nullable', 'in:positive,negative'],
        ]);
    }

    /** Map validated cycle input to the model columns (shared by store + update). */
    private function cycleAttributes(array $data): array
    {
        return [
            'patient_id' => $data['patient_id'],
            'cycle_type' => $data['cycle_type'],
            'protocol' => $data['protocol'],
            'dose' => $data['dose'] ?? null,
            'start_date' => $data['start_date'],
            'current_stage' => $data['current_stage'],
            'stimulation_start_date' => $data['stimulation_start_date'] ?? null,
            'stimulation_end_date' => $data['stimulation_end_date'] ?? null,
            'egg_retrieval_date' => $data['egg_retrieval_date'] ?? null,
            'fertilization_date' => $data['fertilization_date'] ?? null,
            'embryo_transfer_date' => $data['embryo_transfer_date'] ?? null,
            'freezing_note' => $data['freezing_note'] ?? null,
            'is_pregnant' => isset($data['final_result']) ? ($data['final_result'] === 'positive') : null,
        ];
    }

    /** Persist the patient's editable contact fields from the cycle form. */
    private function syncCyclePatient(array $data): void
    {
        $patient = Patient::find($data['patient_id']);
        $history = $patient->medical_history ?? [];
        if (isset($data['age'])) {
            $history['age'] = $data['age'];
        }
        $patient->update([
            'phone' => $data['phone'] ?? $patient->phone,
            'address' => $data['address'] ?? $patient->address,
            'medical_history' => $history,
        ]);
    }

    public function destroyCycle(IvfCycle $cycle)
    {
        $cycle->followups()->delete();
        $cycle->delete();

        return back()->with('status', __('deleted'));
    }

    /* ---------------------------------------------------------------- Surgeries */

    public function surgeries()
    {
        return view('admin.surgeries', [
            'surgeries' => Surgery::with(['patient' => fn ($q) => $q->withTrashed(), 'staff' => fn ($q) => $q->withTrashed()])
                ->orderByDesc('scheduled_date')
                ->get()
                ->map(fn (Surgery $s) => [
                    'id' => $s->id,
                    'patient_id' => $s->patient_id,
                    'patient' => $s->patient?->name ?? '—',
                    'file' => $s->patient?->file_number ?? '',
                    'operation' => $s->surgery_name,
                    'surgery_type' => $s->surgery_type,
                    'type' => __($s->surgery_type),
                    'date' => $s->scheduled_date?->format('Y-m-d'),
                    'time' => $s->scheduled_date?->format('H:i'),
                    'doctor' => $s->doctor_name ?: ($s->staff?->name ?? 'د. محمد عوض'),
                    'doctor_name' => $s->doctor_name,
                    'description' => $s->notes,
                    'cost' => (float) $s->total_cost,
                    'status' => $s->status,
                ])->all(),
            'stats' => $this->surgeryStats(),
            'patientOptions' => $this->patientOptions(),
            'doctorOptions' => $this->doctorNameOptions(),
        ]);
    }

    private function surgeryStats(): array
    {
        return [
            ['label' => 'إجمالي العمليات', 'value' => (string) Surgery::count(), 'icon' => 'activity', 'color' => 'bg-blue-500'],
            ['label' => 'مجدولة', 'value' => (string) Surgery::where('status', 'scheduled')->count(), 'icon' => 'calendar', 'color' => 'bg-yellow-500'],
            ['label' => 'مكتملة', 'value' => (string) Surgery::where('status', 'completed')->count(), 'icon' => 'check-circle', 'color' => 'bg-green-500'],
            ['label' => 'إجمالي التكلفة', 'value' => number_format((float) Surgery::sum('total_cost')), 'icon' => 'trending-up', 'color' => 'bg-purple-500'],
        ];
    }

    public function storeSurgery(Request $request)
    {
        $data = $this->validateSurgery($request);

        Surgery::create([
            'patient_id' => $data['patient_id'],
            'staff_id' => $this->doctorStaffId(),
            'doctor_name' => $data['doctor_name'] ?? null,
            'surgery_type' => $data['surgery_type'],
            'surgery_name' => $data['surgery_name'],
            'scheduled_date' => Carbon::parse($data['date'] . ' ' . ($data['time'] ?? '09:00')),
            'status' => $data['status'],
            'total_cost' => $data['total_cost'] ?? 0,
            'notes' => $data['notes'] ?? null,
        ]);

        return back()->with('status', __('saved'));
    }

    public function updateSurgery(Request $request, Surgery $surgery)
    {
        $data = $this->validateSurgery($request);

        $surgery->update([
            'patient_id' => $data['patient_id'],
            'doctor_name' => $data['doctor_name'] ?? null,
            'surgery_type' => $data['surgery_type'],
            'surgery_name' => $data['surgery_name'],
            'scheduled_date' => Carbon::parse($data['date'] . ' ' . ($data['time'] ?? '09:00')),
            'status' => $data['status'],
            'total_cost' => $data['total_cost'] ?? 0,
            'notes' => $data['notes'] ?? null,
        ]);

        return back()->with('status', __('saved'));
    }

    public function destroySurgery(Surgery $surgery)
    {
        $surgery->delete();

        return back()->with('status', __('deleted'));
    }

    private function validateSurgery(Request $request): array
    {
        return $request->validate([
            'patient_id' => ['required', 'exists:patients,id'],
            'surgery_name' => ['required', 'string', 'max:200'],
            'surgery_type' => ['required', 'in:laparoscopy,hysteroscopy,cesarean,natural_delivery,other'],
            'date' => ['required', 'date'],
            'time' => ['nullable'],
            'status' => ['required', 'in:scheduled,pending,completed,cancelled'],
            'total_cost' => ['nullable', 'numeric', 'min:0'],
            'doctor_name' => ['nullable', 'string', 'max:200'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);
    }

    /** Doctor-name suggestions for the surgery form's datalist. */
    private function doctorNameOptions(): array
    {
        $staff = Staff::whereHas('user', fn ($q) => $q->whereIn('role', ['doctor', 'admin']))->get()->map->name;
        $used = Surgery::whereNotNull('doctor_name')->distinct()->pluck('doctor_name');

        return $staff->merge($used)->push('د. محمد عوض')->filter()->unique()->values()->all();
    }

    /* ---------------------------------------------------------------- Payments / invoices */

    public function payments()
    {
        return view('admin.payments', [
            'invoices' => Invoice::with('patient')->latest()->get()->map(fn (Invoice $inv) => [
                'id' => $inv->id,
                'number' => $inv->invoice_number,
                'patient' => $inv->patient?->name ?? '',
                'service' => $inv->items[0]['label'] ?? '',
                'amount' => (float) $inv->total,
                'method' => $inv->notes,
                'status' => $inv->status,
                'date' => $inv->due_date?->format('Y-m-d') ?? $inv->created_at?->format('Y-m-d'),
            ])->all(),
            'stats' => $this->paymentStats(),
            'patientOptions' => $this->patientOptions(),
        ]);
    }

    private function paymentStats(): array
    {
        $revenue = (float) Payment::where('status', 'paid')->sum('amount');
        $thisMonth = (float) Payment::where('status', 'paid')
            ->whereMonth('paid_at', now()->month)->whereYear('paid_at', now()->year)->sum('amount');

        return [
            ['label' => 'إجمالي الإيرادات', 'value' => number_format($revenue) . ' ج.م', 'icon' => 'dollar-sign', 'color' => 'bg-green-500'],
            ['label' => 'مدفوعات هذا الشهر', 'value' => number_format($thisMonth) . ' ج.م', 'icon' => 'wallet', 'color' => 'bg-blue-500'],
            ['label' => 'فواتير مدفوعة', 'value' => (string) Invoice::where('status', 'paid')->count(), 'icon' => 'check-circle', 'color' => 'bg-purple-500'],
            ['label' => 'فواتير معلقة', 'value' => (string) Invoice::where('status', 'pending')->count(), 'icon' => 'credit-card', 'color' => 'bg-yellow-500'],
        ];
    }

    public function storeInvoice(Request $request)
    {
        $data = $request->validate([
            'patient_id' => ['required', 'exists:patients,id'],
            'service' => ['required', 'string', 'max:200'],
            'amount' => ['required', 'numeric', 'min:0'],
            'payment_method' => ['nullable', 'string', 'max:50'],
            'date' => ['nullable', 'date'],
            'status' => ['required', 'in:pending,paid'],
        ]);

        $paid = $data['status'] === 'paid';
        $date = $data['date'] ?? today()->toDateString();

        $invoice = Invoice::create([
            'invoice_number' => $this->generateInvoiceNumber(),
            'patient_id' => $data['patient_id'],
            'subtotal' => $data['amount'],
            'total' => $data['amount'],
            'paid_amount' => $paid ? $data['amount'] : 0,
            'status' => $data['status'],
            'due_date' => $date,
            'paid_at' => $paid ? $date : null,
            'items' => [['label' => $data['service'], 'amount' => $data['amount']]],
            'notes' => $data['payment_method'] ?? null,
        ]);

        if ($paid) {
            $this->recordPayment($invoice, $data['payment_method'] ?? 'نقدي', $date);
        }

        return back()->with('status', __('saved'));
    }

    public function markInvoicePaid(Invoice $invoice)
    {
        if ($invoice->status !== 'paid') {
            $invoice->update([
                'status' => 'paid',
                'paid_amount' => $invoice->total,
                'paid_at' => now(),
            ]);
            $this->recordPayment($invoice, $invoice->notes ?: 'نقدي', now()->toDateString());
        }

        return back()->with('status', __('saved'));
    }

    public function destroyInvoice(Invoice $invoice)
    {
        DB::transaction(function () use ($invoice) {
            $invoice->payments()->delete();
            $invoice->delete();
        });

        return back()->with('status', __('deleted'));
    }

    private function recordPayment(Invoice $invoice, string $method, string $date): void
    {
        Payment::create([
            'invoice_id' => $invoice->id,
            'amount' => $invoice->total,
            'payment_method' => $method,
            'status' => 'paid',
            'paid_at' => $date,
        ]);
    }

    /* ---------------------------------------------------------------- Reviews */

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

    /* ---------------------------------------------------------------- Staff */

    public function staff()
    {
        $roleLabels = collect(config('clinic.role_labels'))->map(fn ($k) => __($k))->all();

        return view('admin.staff', [
            'staff' => Staff::with('user')->get()->map(fn (Staff $s) => [
                'id' => $s->id,
                'name' => $s->name,
                'first_name_ar' => $s->first_name_ar,
                'last_name_ar' => $s->last_name_ar,
                'title' => $s->title,
                'role' => $roleLabels[$s->user?->role] ?? $s->title,
                'role_key' => $s->user?->role,
                'email' => $s->user?->email,
                'phone' => $s->phone,
                'is_available' => (bool) $s->is_available,
                'status' => $s->is_available ? 'active' : 'vacation',
            ])->all(),
            'roleOptions' => collect(config('clinic.staff_roles'))
                ->map(fn ($role) => ['value' => $role, 'label' => __(config("clinic.role_labels.$role"))])
                ->all(),
        ]);
    }

    public function storeStaff(Request $request)
    {
        $data = $request->validate([
            'first_name_ar' => ['required', 'string', 'max:100'],
            'last_name_ar' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6'],
            'role' => ['required', Rule::in(config('clinic.staff_roles'))],
            'title' => ['nullable', 'string', 'max:100'],
            'phone' => ['nullable', 'string', 'max:20'],
            'specialization' => ['nullable', 'string', 'max:200'],
        ]);

        DB::transaction(function () use ($data) {
            $user = User::create([
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'role' => $data['role'],
                'is_active' => true,
                'email_verified' => true,
            ]);

            Staff::create([
                'user_id' => $user->id,
                'first_name_ar' => $data['first_name_ar'],
                'last_name_ar' => $data['last_name_ar'],
                'title' => $data['title'] ?? null,
                'phone' => $data['phone'] ?? null,
                'specialization' => $data['specialization'] ?? null,
                'is_available' => true,
            ]);
        });

        return back()->with('status', __('saved'));
    }

    public function updateStaff(Request $request, Staff $staff)
    {
        $data = $request->validate([
            'first_name_ar' => ['required', 'string', 'max:100'],
            'last_name_ar' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($staff->user_id)],
            'role' => ['required', Rule::in(config('clinic.staff_roles'))],
            'title' => ['nullable', 'string', 'max:100'],
            'phone' => ['nullable', 'string', 'max:20'],
            'password' => ['nullable', 'string', 'min:6'],
            'is_available' => ['nullable', 'boolean'],
        ]);

        $staff->update([
            'first_name_ar' => $data['first_name_ar'],
            'last_name_ar' => $data['last_name_ar'],
            'title' => $data['title'] ?? $staff->title,
            'phone' => $data['phone'] ?? $staff->phone,
            'is_available' => $request->boolean('is_available'),
        ]);

        // Reactivate the login when the member is marked available; keep it usable.
        $userUpdate = [
            'email' => $data['email'],
            'role' => $data['role'],
            'is_active' => $request->boolean('is_available'),
        ];
        // Optional password reset — only when a new one is provided.
        if (! empty($data['password'])) {
            $userUpdate['password'] = Hash::make($data['password']);
        }
        $staff->user?->update($userUpdate);

        return back()->with('status', __('saved'));
    }

    public function destroyStaff(Staff $staff)
    {
        DB::transaction(function () use ($staff) {
            $staff->user?->update(['is_active' => false]);
            $staff->delete();
        });

        return back()->with('status', __('deleted'));
    }

    /* ---------------------------------------------------------------- Branches */

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
        $data = $this->validateBranch($request);

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
        $data = $this->validateBranch($request);

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

    private function validateBranch(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:200'],
            'address' => ['required', 'string', 'max:500'],
            'phone' => ['required', 'string', 'max:20'],
            'hours' => ['nullable', 'string', 'max:100'],
        ]);
    }

    /* ---------------------------------------------------------------- Messages */

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

    /* ---------------------------------------------------------------- Settings */

    public function settings()
    {
        return view('admin.settings', [
            'socialLinks' => json_decode(Setting::get('social_links', '[]'), true) ?: [],
        ]);
    }

    public function updateSettings(Request $request)
    {
        $data = $request->validate([
            'site_name_ar' => ['nullable', 'string', 'max:200'],
            'site_name_en' => ['nullable', 'string', 'max:200'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'whatsapp' => ['nullable', 'string', 'max:30'],
            'meta_title' => ['nullable', 'string', 'max:200'],
            'meta_desc' => ['nullable', 'string', 'max:500'],
            'social' => ['nullable', 'array'],
            'social.*.platform' => ['required_with:social', 'string', 'in:facebook,instagram,twitter,youtube,tiktok,linkedin'],
            'social.*.url' => ['nullable', 'url', 'max:300'],
        ]);

        foreach (['site_name_ar', 'site_name_en', 'email', 'phone', 'whatsapp', 'meta_title', 'meta_desc'] as $key) {
            if (array_key_exists($key, $data)) {
                Setting::put($key, $data[$key], 'general');
            }
        }

        // Social links: keep only rows with a URL; store as a JSON array.
        $social = collect($data['social'] ?? [])
            ->filter(fn ($row) => ! empty($row['url']))
            ->map(fn ($row) => ['platform' => $row['platform'], 'url' => $row['url']])
            ->values()->all();
        Setting::put('social_links', json_encode($social, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), 'social');

        return back()->with('status', __('saved'));
    }

    public function permissions()
    {
        return view('admin.permissions');
    }

    /** Flip one (role, feature) cell in the permission matrix. Admin is never toggleable. */
    public function togglePermission(Request $request)
    {
        $data = $request->validate([
            'role' => ['required', Rule::in(config('clinic.staff_roles'))],
            'feature' => ['required', 'string'],
        ]);

        if ($data['role'] !== 'admin') {
            \App\Support\Access::toggle($data['role'], $data['feature']);
        }

        return back()->with('status', __('saved'));
    }

    /** Any signed-in staff member changes their own password. */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $request->user()->update(['password' => Hash::make($request->input('password'))]);

        return back()->with('status', __('passwordChanged'));
    }

    /* ---------------------------------------------------------------- Helpers */

    /** Options for a patient <select> across the admin write forms. */
    private function patientOptions(): array
    {
        return Patient::orderBy('file_number')->get()
            ->map(fn (Patient $p) => ['value' => $p->id, 'label' => $p->name . ' — ' . $p->file_number])
            ->all();
    }

    private function branchOptions(): array
    {
        return Branch::orderByDesc('is_main')->get()
            ->map(fn (Branch $b) => ['value' => $b->id, 'label' => $b->name_ar])
            ->all();
    }

    private function serviceOptions(): array
    {
        return Service::where('is_active', true)->get()
            ->map(fn (Service $s) => ['value' => $s->id, 'label' => $s->name_ar])
            ->all();
    }

    private function doctorStaffId(): ?string
    {
        return Staff::whereHas('user', fn ($q) => $q->where('role', 'doctor'))->value('id')
            ?? Staff::value('id');
    }

    private function generateFileNumber(): string
    {
        return Patient::generateFileNumber();
    }

    private function generateInvoiceNumber(): string
    {
        $year = now()->format('Y');
        $seq = Invoice::count() + 1;

        do {
            $candidate = sprintf('INV-%s-%04d', $year, $seq);
            $seq++;
        } while (Invoice::where('invoice_number', $candidate)->exists());

        return $candidate;
    }
}
