<?php

namespace Database\Seeders;

use App\Models\Appointment;
use App\Models\Branch;
use App\Models\IvfCycle;
use App\Models\IvfFollowup;
use App\Models\Patient;
use App\Models\Service;
use App\Models\Setting;
use App\Models\Staff;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Seeds the exact demo state the Next.js prototype rendered from hardcoded arrays.
 * Hardcoded UUIDs keep re-seeds stable. Re-run with:
 *   php artisan migrate:fresh --seed
 */
class DemoSeeder extends Seeder
{
    private function uuid(string $group, int $n): string
    {
        return sprintf('00000000-0000-4000-%s-%012d', $group, $n);
    }

    public function run(): void
    {
        $this->seedUsersAndStaff();
        $branches = $this->seedBranches();
        $services = $this->seedServices();
        $patients = $this->seedPatients();
        $this->seedAppointments($patients, $branches, $services);
        $this->seedIvfCycles($patients);
        $this->seedSettings();
    }

    private function seedUsersAndStaff(): void
    {
        User::create([
            'id' => $this->uuid('8000', 1),
            'email' => 'admin@dr-awad.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'is_active' => true,
            'email_verified' => true,
        ]);

        $doctor = User::create([
            'id' => $this->uuid('8000', 2),
            'email' => 'dr.mohamed@dr-awad.com',
            'password' => Hash::make('password'),
            'role' => 'doctor',
            'is_active' => true,
            'email_verified' => true,
        ]);

        Staff::create([
            'id' => $this->uuid('8007', 1),
            'user_id' => $doctor->id,
            'first_name_ar' => 'محمد',
            'last_name_ar' => 'عوض',
            'first_name_en' => 'Mohamed',
            'last_name_en' => 'Awad',
            'title' => 'استشاري',
            'specialization' => 'النساء والتوليد والحقن المجهري وجراحات المناظير',
            'image_url' => '/images/dr-mohamed-awad.jpg',
            'is_available' => true,
        ]);
    }

    /** @return array<string, Branch> */
    private function seedBranches(): array
    {
        $rows = [
            ['key' => 'dokki', 'name' => 'فرع الدقي - القاهرة', 'short' => 'فرع الدقي', 'address' => 'شارع التحرير، الدقي، الجيزة', 'phone' => '+20 123 456 7890', 'email' => 'dokki@dr-awad.com', 'hours' => 'السبت - الخميس: 9 ص - 9 م', 'lat' => 30.0444, 'lng' => 31.2357, 'main' => true],
            ['key' => 'maadi', 'name' => 'فرع المعادي - القاهرة', 'short' => 'فرع المعادي', 'address' => 'شارع 9، المعادي، القاهرة', 'phone' => '+20 123 456 7891', 'email' => 'maadi@dr-awad.com', 'hours' => 'السبت - الخميس: 10 ص - 8 م', 'lat' => 29.9626, 'lng' => 31.2497, 'main' => false],
            ['key' => 'alex', 'name' => 'فرع الإسكندرية', 'short' => 'فرع الإسكندرية', 'address' => 'سان ستيفانو، الإسكندرية', 'phone' => '+20 123 456 7892', 'email' => 'alex@dr-awad.com', 'hours' => 'السبت - الأربعاء: 10 ص - 6 م', 'lat' => 31.2435, 'lng' => 29.9619, 'main' => false],
        ];

        $branches = [];
        foreach ($rows as $i => $row) {
            $branches[$row['key']] = Branch::create([
                'id' => $this->uuid('8001', $i + 1),
                'name_ar' => $row['name'],
                'address_ar' => $row['address'],
                'phone' => $row['phone'],
                'whatsapp' => $row['phone'],
                'email' => $row['email'],
                'latitude' => $row['lat'],
                'longitude' => $row['lng'],
                'google_maps_url' => "https://maps.google.com/?q={$row['lat']},{$row['lng']}",
                // display / short live in working_hours jsonb — parity convention.
                'working_hours' => ['display' => $row['hours'], 'short' => $row['short']],
                'is_main' => $row['main'],
                'is_active' => true,
            ]);
        }

        return $branches;
    }

    /** @return array<string, Service> */
    private function seedServices(): array
    {
        $rows = [
            'pregnancy' => ['name' => 'متابعة حمل', 'price' => 400],
            'consultation' => ['name' => 'كشف طبي', 'price' => 500],
            'ivf-consultation' => ['name' => 'استشارة حقن مجهري', 'price' => 700],
            'followup' => ['name' => 'متابعة', 'price' => 300],
            'sonar-4d' => ['name' => 'سونار 4D', 'price' => 600],
        ];

        $services = [];
        $i = 1;
        foreach ($rows as $key => $row) {
            $services[$key] = Service::create([
                'id' => $this->uuid('8002', $i++),
                'name_ar' => $row['name'],
                'price' => $row['price'],
                'is_active' => true,
            ]);
        }

        return $services;
    }

    /** @return array<string, Patient> — keyed by the short name used in the IVF/dashboard views. */
    private function seedPatients(): array
    {
        $rows = [
            ['first' => 'سارة', 'last' => 'أحمد محمد', 'short' => 'سارة أحمد', 'file' => 'P2024001', 'phone' => '01012345678', 'email' => 'sara@email.com', 'age' => 32, 'type' => 'حقن مجهري', 'visit' => '2024-01-15', 'status' => 'active'],
            ['first' => 'منى', 'last' => 'خالد إبراهيم', 'short' => 'منى خالد', 'file' => 'P2024002', 'phone' => '01098765432', 'email' => 'mona@email.com', 'age' => 28, 'type' => 'متابعة حمل', 'visit' => '2024-01-14', 'status' => 'active'],
            ['first' => 'هالة', 'last' => 'محمد عبدالله', 'short' => 'هالة محمد', 'file' => 'P2024003', 'phone' => '01234567890', 'email' => 'hala@email.com', 'age' => 35, 'type' => 'جراحات مناظير', 'visit' => '2024-01-10', 'status' => 'active'],
            ['first' => 'نورا', 'last' => 'أحمد سعيد', 'short' => 'نورا أحمد', 'file' => 'P2024004', 'phone' => '01111222333', 'email' => 'noura@email.com', 'age' => 30, 'type' => 'علاج تكيس', 'visit' => '2024-01-08', 'status' => 'archived'],
            ['first' => 'ريم', 'last' => 'محمود علي', 'short' => 'ريم سعيد', 'file' => 'P2024005', 'phone' => '01555666777', 'email' => 'reem@email.com', 'age' => 27, 'type' => 'متابعة حمل', 'visit' => '2024-01-12', 'status' => 'active'],
        ];

        $patients = [];
        foreach ($rows as $i => $row) {
            $patients[$row['file']] = Patient::create([
                'id' => $this->uuid('8003', $i + 1),
                'file_number' => $row['file'],
                'first_name_ar' => $row['first'],
                'last_name_ar' => $row['last'],
                'phone' => $row['phone'],
                'email' => $row['email'],
                'date_of_birth' => now()->subYears($row['age'])->toDateString(),
                'gender' => 'female',
                // Demo display fields — parity convention (see plan).
                'medical_history' => [
                    'type' => $row['type'],
                    'last_visit' => $row['visit'],
                    'status' => $row['status'],
                    'age' => $row['age'],
                    'short_name' => $row['short'],
                ],
            ]);
        }

        return $patients;
    }

    private function seedAppointments(array $patients, array $branches, array $services): void
    {
        $rows = [
            ['file' => 'P2024001', 'service' => 'pregnancy', 'time' => '09:00', 'branch' => 'dokki', 'status' => 'confirmed', 'notes' => null],
            ['file' => 'P2024002', 'service' => 'consultation', 'time' => '09:30', 'branch' => 'dokki', 'status' => 'waiting', 'notes' => null],
            ['file' => 'P2024003', 'service' => 'ivf-consultation', 'time' => '10:00', 'branch' => 'dokki', 'status' => 'confirmed', 'notes' => 'مراجعة التحاليل'],
            ['file' => 'P2024004', 'service' => 'followup', 'time' => '10:30', 'branch' => 'maadi', 'status' => 'pending', 'notes' => null],
            ['file' => 'P2024005', 'service' => 'sonar-4d', 'time' => '11:00', 'branch' => 'dokki', 'status' => 'cancelled', 'notes' => 'تم الإلغاء بطلب المريضة'],
        ];

        foreach ($rows as $i => $row) {
            Appointment::create([
                'id' => $this->uuid('8004', $i + 1),
                'patient_id' => $patients[$row['file']]->id,
                'branch_id' => $branches[$row['branch']]->id,
                'service_id' => $services[$row['service']]->id,
                'appointment_date' => '2024-01-20',
                'appointment_time' => $row['time'],
                'status' => $row['status'],
                'notes' => $row['notes'],
            ]);
        }
    }

    private function seedIvfCycles(array $patients): void
    {
        $rows = [
            ['file' => 'P2024001', 'number' => 1, 'type' => 'ICSI', 'protocol' => 'Long Protocol', 'start' => '2024-01-05', 'stage' => 'stimulation', 'day' => 8, 'next' => '2024-01-20'],
            ['file' => 'P2024002', 'number' => 2, 'type' => 'IVF', 'protocol' => 'Antagonist', 'start' => '2024-01-03', 'stage' => 'egg_retrieval', 'day' => 12, 'next' => '2024-01-15'],
            ['file' => 'P2024003', 'number' => 1, 'type' => 'ICSI', 'protocol' => 'Short Protocol', 'start' => '2024-01-10', 'stage' => 'consultation', 'day' => 3, 'next' => '2024-01-18'],
            ['file' => 'P2024004', 'number' => 1, 'type' => 'IUI', 'protocol' => 'Natural', 'start' => '2024-01-08', 'stage' => 'embryo_transfer', 'day' => 15, 'next' => '2024-01-25'],
        ];

        foreach ($rows as $i => $row) {
            $cycle = IvfCycle::create([
                'id' => $this->uuid('8005', $i + 1),
                'patient_id' => $patients[$row['file']]->id,
                'staff_id' => $this->uuid('8007', 1),
                'cycle_number' => $row['number'],
                'cycle_type' => $row['type'],
                'protocol' => $row['protocol'],
                'start_date' => $row['start'],
                'current_stage' => $row['stage'],
            ]);

            // day_of_cycle + next_appointment live on the followup (schema-faithful).
            IvfFollowup::create([
                'id' => $this->uuid('8006', $i + 1),
                'cycle_id' => $cycle->id,
                'followup_date' => $row['start'],
                'day_of_cycle' => $row['day'],
                'next_appointment' => $row['next'],
            ]);
        }
    }

    /** Dashboard / IVF / appointment stat blocks — seeded verbatim as JSON settings. */
    private function seedSettings(): void
    {
        $settings = [
            'dashboard_stats' => [
                ['title' => 'إجمالي المريضات', 'value' => '2,543', 'change' => '+12.5%', 'trend' => 'up', 'icon' => 'users', 'color' => 'bg-blue-500'],
                ['title' => 'مواعيد اليوم', 'value' => '28', 'change' => '+5.2%', 'trend' => 'up', 'icon' => 'calendar', 'color' => 'bg-green-500'],
                ['title' => 'العمليات الشهرية', 'value' => '156', 'change' => '-2.3%', 'trend' => 'down', 'icon' => 'stethoscope', 'color' => 'bg-purple-500'],
                ['title' => 'الإيرادات الشهرية', 'value' => '485,000', 'change' => '+18.7%', 'trend' => 'up', 'icon' => 'credit-card', 'color' => 'bg-yellow-500', 'currency' => 'جنيه'],
            ],
            'dashboard_recent_appointments' => [
                ['name' => 'سارة أحمد', 'service' => 'متابعة حمل', 'time' => '09:00 ص', 'status' => 'confirmed'],
                ['name' => 'منى خالد', 'service' => 'كشف طبي', 'time' => '09:30 ص', 'status' => 'waiting'],
                ['name' => 'هالة محمد', 'service' => 'استشارة حقن مجهري', 'time' => '10:00 ص', 'status' => 'confirmed'],
                ['name' => 'نورا أحمد', 'service' => 'متابعة', 'time' => '10:30 ص', 'status' => 'pending'],
                ['name' => 'ريم سعيد', 'service' => 'سونار', 'time' => '11:00 ص', 'status' => 'confirmed'],
            ],
            'dashboard_ivf_stats' => [
                ['label' => 'دورات نشطة', 'value' => '24', 'icon' => 'activity', 'color' => 'text-blue-500'],
                ['label' => 'سحب بويضات اليوم', 'value' => '3', 'icon' => 'baby', 'color' => 'text-pink-500'],
                ['label' => 'زرع أجنة اليوم', 'value' => '2', 'icon' => 'trending-up', 'color' => 'text-green-500'],
                ['label' => 'نسبة النجاح الشهرية', 'value' => '68%', 'icon' => 'arrow-up-right', 'color' => 'text-purple-500'],
            ],
            'ivf_page_stats' => [
                ['label' => 'دورات نشطة', 'value' => '24', 'icon' => 'activity', 'color' => 'bg-blue-500'],
                ['label' => 'سحب بويضات اليوم', 'value' => '3', 'icon' => 'flask-conical', 'color' => 'bg-purple-500'],
                ['label' => 'زرع أجنة اليوم', 'value' => '2', 'icon' => 'baby', 'color' => 'bg-pink-500'],
                ['label' => 'نسبة النجاح', 'value' => '68%', 'icon' => 'trending-up', 'color' => 'bg-green-500'],
            ],
            'ivf_today_tasks' => [
                ['time' => '09:00', 'task' => 'متابعة تنشيط - سارة أحمد', 'type' => 'follow-up'],
                ['time' => '10:00', 'task' => 'سحب بويضات - منى خالد', 'type' => 'retrieval'],
                ['time' => '11:30', 'task' => 'زرع أجنة - نورا أحمد', 'type' => 'transfer'],
                ['time' => '14:00', 'task' => 'نتيجة تحليل حمل - ريم سعيد', 'type' => 'test'],
            ],
            'appointments_stats' => [
                ['label' => 'إجمالي المواعيد', 'value' => 28, 'color' => 'bg-blue-500'],
                ['label' => 'مؤكدة', 'value' => 18, 'color' => 'bg-green-500'],
                ['label' => 'في الانتظار', 'value' => 6, 'color' => 'bg-yellow-500'],
                ['label' => 'ملغية', 'value' => 4, 'color' => 'bg-red-500'],
            ],
        ];

        $i = 1;
        foreach ($settings as $key => $value) {
            Setting::create([
                'id' => $this->uuid('8008', $i++),
                'key' => $key,
                'value' => json_encode($value, JSON_UNESCAPED_UNICODE),
                'type' => 'json',
                'group' => 'dashboard',
            ]);
        }
    }
}
