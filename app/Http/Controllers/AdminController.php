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
        ]);
    }

    /* ---------------------------------------------------------------- Patients */

    public function patients()
    {
        return view('admin.patients', [
            'patients' => Patient::orderBy('file_number')->get(),
        ]);
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

        return back()->with('status', __('saved'));
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
            'appointments' => Appointment::with(['patient', 'branch', 'service'])
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
            'appointment_time' => ['required'],
            'status' => ['required', 'in:pending,confirmed,waiting,completed,cancelled,no_show'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        Appointment::create($data + ['type' => 'clinic']);

        return back()->with('status', __('saved'));
    }

    public function updateAppointment(Request $request, Appointment $appointment)
    {
        $data = $request->validate([
            'branch_id' => ['required', 'exists:branches,id'],
            'service_id' => ['nullable', 'exists:services,id'],
            'appointment_date' => ['required', 'date'],
            'appointment_time' => ['required'],
            'status' => ['required', 'in:pending,confirmed,waiting,completed,cancelled,no_show'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

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
            'cycles' => IvfCycle::with(['patient', 'latestFollowup'])
                ->orderBy('cycle_number')
                ->orderBy('start_date')
                ->get(),
            'stats' => $this->ivfPageStats(),
            'tasks' => Setting::json('ivf_today_tasks'),
            'patientOptions' => $this->patientOptions(),
        ]);
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
        $data = $request->validate([
            'patient_id' => ['required', 'exists:patients,id'],
            'cycle_type' => ['required', 'string', 'max:50'],
            'protocol' => ['required', 'string', 'max:100'],
            'start_date' => ['required', 'date'],
            'current_stage' => ['required', 'in:consultation,stimulation,egg_retrieval,fertilization,embryo_transfer,pregnancy_test,completed'],
            'day_of_cycle' => ['nullable', 'integer', 'min:1'],
            'next_appointment' => ['nullable', 'date'],
        ]);

        $number = IvfCycle::where('patient_id', $data['patient_id'])->max('cycle_number') + 1;

        $cycle = IvfCycle::create([
            'patient_id' => $data['patient_id'],
            'staff_id' => $this->doctorStaffId(),
            'cycle_number' => $number,
            'cycle_type' => $data['cycle_type'],
            'protocol' => $data['protocol'],
            'start_date' => $data['start_date'],
            'current_stage' => $data['current_stage'],
        ]);

        IvfFollowup::create([
            'cycle_id' => $cycle->id,
            'followup_date' => $data['start_date'],
            'day_of_cycle' => $data['day_of_cycle'] ?? 1,
            'next_appointment' => $data['next_appointment'] ?? null,
        ]);

        return back()->with('status', __('saved'));
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
            'surgeries' => Surgery::with(['patient', 'staff'])
                ->orderByDesc('scheduled_date')
                ->get()
                ->map(fn (Surgery $s) => [
                    'id' => $s->id,
                    'patient_id' => $s->patient_id,
                    'patient' => $s->patient?->name ?? '',
                    'file' => $s->patient?->file_number ?? '',
                    'operation' => $s->surgery_name,
                    'surgery_type' => $s->surgery_type,
                    'type' => __($s->surgery_type),
                    'date' => $s->scheduled_date?->format('Y-m-d'),
                    'time' => $s->scheduled_date?->format('H:i'),
                    'doctor' => $s->staff?->name ?? 'د. محمد عوض',
                    'cost' => (float) $s->total_cost,
                    'status' => $s->status,
                ])->all(),
            'stats' => $this->surgeryStats(),
            'patientOptions' => $this->patientOptions(),
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
            'surgery_type' => $data['surgery_type'],
            'surgery_name' => $data['surgery_name'],
            'scheduled_date' => Carbon::parse($data['date'] . ' ' . ($data['time'] ?? '09:00')),
            'status' => $data['status'],
            'total_cost' => $data['total_cost'] ?? 0,
        ]);

        return back()->with('status', __('saved'));
    }

    public function updateSurgery(Request $request, Surgery $surgery)
    {
        $data = $this->validateSurgery($request);

        $surgery->update([
            'patient_id' => $data['patient_id'],
            'surgery_type' => $data['surgery_type'],
            'surgery_name' => $data['surgery_name'],
            'scheduled_date' => Carbon::parse($data['date'] . ' ' . ($data['time'] ?? '09:00')),
            'status' => $data['status'],
            'total_cost' => $data['total_cost'] ?? 0,
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
        ]);
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
        $year = now()->format('Y');
        $seq = Patient::withTrashed()->count() + 1;

        do {
            $candidate = 'P' . $year . str_pad((string) $seq, 3, '0', STR_PAD_LEFT);
            $seq++;
        } while (Patient::withTrashed()->where('file_number', $candidate)->exists());

        return $candidate;
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
