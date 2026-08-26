<?php

namespace Database\Seeders;

use App\Models\Appointment;
use App\Models\Branch;
use App\Models\Content;
use App\Models\Invoice;
use App\Models\IvfCycle;
use App\Models\IvfFollowup;
use App\Models\MedicalRecord;
use App\Models\Message;
use App\Models\Patient;
use App\Models\Payment;
use App\Models\Review;
use App\Models\Service;
use App\Models\Setting;
use App\Models\Staff;
use App\Models\Surgery;
use App\Models\User;
use App\Support\ClinicData;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\App;
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
        $this->seedSurgeries($patients, $branches);
        $this->seedBilling($patients, $services);
        $this->seedContent();
        $this->seedSiteContent();
        $this->seedEngagement();
        $this->seedSettings();
    }

    /** Scheduled/completed operations for the surgeries admin page. */
    private function seedSurgeries(array $patients, array $branches): void
    {
        $doctorStaffId = $this->uuid('8007', 1);
        $rows = [
            ['file' => 'P2024001', 'type' => 'hysteroscopy', 'name' => 'منظار رحم تشخيصي', 'date' => '2024-01-22 10:00', 'status' => 'scheduled', 'cost' => 12000],
            ['file' => 'P2024002', 'type' => 'laparoscopy', 'name' => 'استئصال كيس مبيض بالمنظار', 'date' => '2024-01-18 12:30', 'status' => 'completed', 'cost' => 18000],
            ['file' => 'P2024003', 'type' => 'laparoscopy', 'name' => 'إزالة ورم ليفي بالمنظار', 'date' => '2024-01-25 09:30', 'status' => 'pending', 'cost' => 22000],
            ['file' => 'P2024005', 'type' => 'cesarean', 'name' => 'ولادة قيصرية', 'date' => '2024-01-15 08:00', 'status' => 'completed', 'cost' => 25000],
        ];

        foreach ($rows as $i => $row) {
            Surgery::create([
                'id' => $this->uuid('800c', $i + 1),
                'patient_id' => $patients[$row['file']]->id,
                'staff_id' => $doctorStaffId,
                'branch_id' => $branches['beni-mazar']->id,
                'surgery_type' => $row['type'],
                'surgery_name' => $row['name'],
                'scheduled_date' => $row['date'],
                'status' => $row['status'],
                'total_cost' => $row['cost'],
            ]);
        }
    }

    /** Invoices (+ a payment for the paid ones) for the payments admin page. */
    private function seedBilling(array $patients, array $services): void
    {
        $rows = [
            ['file' => 'P2024001', 'service' => 'استشارة حقن مجهري', 'amount' => 700, 'method' => 'فيزا', 'status' => 'paid', 'date' => '2024-01-20'],
            ['file' => 'P2024002', 'service' => 'متابعة حمل + سونار 4D', 'amount' => 600, 'method' => 'نقدي', 'status' => 'paid', 'date' => '2024-01-19'],
            ['file' => 'P2024003', 'service' => 'كشف طبي عام', 'amount' => 500, 'method' => 'فودافون كاش', 'status' => 'pending', 'date' => '2024-01-18'],
            ['file' => 'P2024005', 'service' => 'استشارة جراحية', 'amount' => 600, 'method' => 'فيزا', 'status' => 'paid', 'date' => '2024-01-17'],
        ];

        foreach ($rows as $i => $row) {
            $paid = $row['status'] === 'paid';
            $invoice = Invoice::create([
                'id' => $this->uuid('800d', $i + 1),
                'invoice_number' => sprintf('INV-2024-%03d', $i + 1),
                'patient_id' => $patients[$row['file']]->id,
                'subtotal' => $row['amount'],
                'total' => $row['amount'],
                'paid_amount' => $paid ? $row['amount'] : 0,
                'status' => $row['status'],
                'due_date' => $row['date'],
                'paid_at' => $paid ? $row['date'] : null,
                'items' => [['label' => $row['service'], 'amount' => $row['amount']]],
                'notes' => $row['method'],
            ]);

            if ($paid) {
                Payment::create([
                    'id' => $this->uuid('800e', $i + 1),
                    'invoice_id' => $invoice->id,
                    'amount' => $row['amount'],
                    'payment_method' => $row['method'],
                    'status' => 'paid',
                    'paid_at' => $row['date'],
                ]);
            }
        }
    }

    /** Published articles / FAQ entries for the content admin page. */
    /** Blog + videos, seeded from ClinicData so the public pages look identical but are now editable. */
    private function seedContent(): void
    {
        App::setLocale('ar');

        foreach (ClinicData::articles() as $i => $a) {
            Content::create([
                'id' => $this->uuid('800f', $i + 1),
                'type' => 'article',
                'title_ar' => $a['title'],
                'excerpt_ar' => $a['excerpt'],
                'content_ar' => $a['excerpt'],
                'meta' => ['category' => $a['category'], 'author_name' => $a['author'], 'read_time' => $a['readTime']],
                'is_published' => true,
                'published_at' => \Illuminate\Support\Carbon::parse($a['date']),
            ]);
        }

        foreach (ClinicData::videos() as $i => $v) {
            Content::create([
                'id' => $this->uuid('8010', $i + 1),
                'type' => 'video',
                'title_ar' => $v['title'],
                'excerpt_ar' => $v['description'],
                'video_url' => $v['id'],
                'meta' => ['category' => $v['category'], 'duration' => $v['duration'], 'views_label' => $v['views']],
                'is_published' => true,
                'published_at' => \Illuminate\Support\Carbon::parse($v['date']),
            ]);
        }
    }

    /** Featured service categories (for /services) + the editable About-page settings. */
    private function seedSiteContent(): void
    {
        App::setLocale('ar');
        $servicesAr = ClinicData::services();
        App::setLocale('en');
        $servicesEn = ClinicData::services();
        App::setLocale('ar');

        foreach ($servicesAr as $i => $s) {
            Service::create([
                'id' => $this->uuid('8011', $i + 1),
                'name_ar' => $s['title'],
                'name_en' => $servicesEn[$i]['title'] ?? null,
                'description_ar' => $s['description'],
                'description_en' => $servicesEn[$i]['description'] ?? null,
                'features' => $s['features'],
                'icon' => $s['icon'],
                'color' => $s['color'],
                'image_url' => $s['image'],
                'slug' => $s['id'],
                'is_active' => true,
                'is_featured' => true,
                'sort_order' => $i,
            ]);
        }

        // About-page lists, stored bilingually as JSON settings.
        $this->putBilingual('about_qualifications', 'qualifications');
        $this->putBilingual('about_experience', 'experience');
        $this->putBilingual('about_memberships', 'memberships');

        App::setLocale('ar');
        $whyAr = ClinicData::whyChooseUs();
        App::setLocale('en');
        $whyEn = ClinicData::whyChooseUs();
        App::setLocale('ar');
        $why = collect($whyAr)->map(fn ($r, $i) => [
            'icon' => $r['icon'],
            'title_ar' => $r['title'],
            'title_en' => $whyEn[$i]['title'] ?? '',
            'desc_ar' => $r['desc'],
            'desc_en' => $whyEn[$i]['desc'] ?? '',
        ])->all();
        Setting::put('about_why', json_encode($why, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), 'about');
    }

    /** Zip the Arabic + English variant of a ClinicData list method into a bilingual JSON setting. */
    private function putBilingual(string $settingKey, string $method): void
    {
        App::setLocale('ar');
        $ar = ClinicData::{$method}();
        App::setLocale('en');
        $en = ClinicData::{$method}();
        App::setLocale('ar');

        $rows = collect($ar)->map(fn ($v, $i) => ['ar' => $v, 'en' => $en[$i] ?? ''])->all();
        Setting::put($settingKey, json_encode($rows, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), 'about');
    }

    /** Contact messages + patient reviews for the admin pages. */
    private function seedEngagement(): void
    {
        $messages = [
            ['name' => 'سارة أحمد', 'email' => 'sara@email.com', 'phone' => '01012345678', 'subject' => 'استفسار عن الحقن المجهري', 'message' => 'السلام عليكم، أريد الاستفسار عن مواعيد وتكلفة الحقن المجهري. شكراً لكم.', 'status' => 'unread'],
            ['name' => 'منى خالد', 'email' => 'mona@email.com', 'phone' => '01087654321', 'subject' => 'حجز موعد متابعة حمل', 'message' => 'أريد حجز موعد لمتابعة الحمل في الأسبوع القادم إن أمكن.', 'status' => 'unread'],
            ['name' => 'نورا محمد', 'email' => 'nora@email.com', 'phone' => '01011223344', 'subject' => 'شكر وتقدير', 'message' => 'شكراً جزيلاً على التعامل الرائع والمتابعة المستمرة.', 'status' => 'read'],
        ];
        foreach ($messages as $i => $m) {
            Message::create(array_merge($m, [
                'id' => $this->uuid('800a', $i + 1),
                'source' => 'contact_form',
            ]));
        }

        $reviews = [
            ['patient_name' => 'سارة أحمد', 'rating' => 5, 'content_ar' => 'دكتور ممتاز ومتابعة رائعة، أنصح به بشدة.', 'title_ar' => 'الحقن المجهري', 'is_approved' => true],
            ['patient_name' => 'منى خالد', 'rating' => 5, 'content_ar' => 'تعامل راقٍ ونتائج مبهرة، شكراً لكم.', 'title_ar' => 'متابعة الحمل', 'is_approved' => true],
            ['patient_name' => 'نورا محمد', 'rating' => 4, 'content_ar' => 'تجربة جيدة جداً والتعافي كان سريعاً.', 'title_ar' => 'جراحات المناظير', 'is_approved' => false],
            ['patient_name' => 'ريم سعيد', 'rating' => 5, 'content_ar' => 'أفضل عيادة نساء وتوليد تعاملت معها.', 'title_ar' => 'النساء والتوليد', 'is_approved' => true],
        ];
        foreach ($reviews as $i => $r) {
            Review::create(array_merge($r, ['id' => $this->uuid('800b', $i + 1)]));
        }
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
            'phone' => '01005078266',
            'image_url' => '/images/dr-mohamed-awad.jpg',
            'is_available' => true,
        ]);

        // Additional staff accounts (all password: "password") so every role can sign in,
        // each with a matching Staff profile for the staff admin page.
        $extra = [
            3 => ['email' => 'nurse@dr-awad.com', 'role' => 'nurse', 'first' => 'فاطمة', 'last' => 'سيد', 'title' => 'طاقم تمريض', 'phone' => '01012345671', 'available' => true],
            4 => ['email' => 'reception@dr-awad.com', 'role' => 'receptionist', 'first' => 'أحمد', 'last' => 'علي', 'title' => 'موظف استقبال', 'phone' => '01012345672', 'available' => true],
            5 => ['email' => 'lab@dr-awad.com', 'role' => 'lab_technician', 'first' => 'سمر', 'last' => 'حسن', 'title' => 'فني معمل', 'phone' => '01012345673', 'available' => false],
        ];
        foreach ($extra as $n => $row) {
            $user = User::create([
                'id' => $this->uuid('8000', $n),
                'email' => $row['email'],
                'password' => Hash::make('password'),
                'role' => $row['role'],
                'is_active' => true,
                'email_verified' => true,
            ]);

            Staff::create([
                'id' => $this->uuid('8007', $n - 1),
                'user_id' => $user->id,
                'first_name_ar' => $row['first'],
                'last_name_ar' => $row['last'],
                'title' => $row['title'],
                'phone' => $row['phone'],
                'is_available' => $row['available'],
            ]);
        }
    }

    /** @return array<string, Branch> */
    private function seedBranches(): array
    {
        $rows = [
            ['key' => 'beni-mazar', 'name' => 'فرع بني مزار', 'short' => 'فرع بني مزار', 'address' => 'شارع موقف صندفا - بجوار مسجد سيدنا حمزة', 'phone' => '01005078266', 'email' => 'info@dr-awad.com', 'hours' => 'يومياً من 9 ص - 10 م', 'lat' => 28.5060, 'lng' => 30.8010, 'main' => true],
            ['key' => 'sheikh-zayed', 'name' => 'فرع الشيخ زايد', 'short' => 'فرع الشيخ زايد', 'address' => 'الشيخ زايد - القاهرة', 'phone' => '01005078266', 'email' => 'info@dr-awad.com', 'hours' => 'يومياً من 9 ص - 10 م', 'lat' => 30.0755, 'lng' => 30.9760, 'main' => false],
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

        // A signed-in patient account for the portal (sara@email.com / password).
        $portalUser = User::create([
            'id' => $this->uuid('8000', 6),
            'email' => 'sara@email.com',
            'password' => Hash::make('password'),
            'role' => 'patient',
            'is_active' => true,
            'email_verified' => true,
        ]);
        $patients['P2024001']->update(['user_id' => $portalUser->id]);

        // A couple of medical records so the portal dashboard shows real data.
        foreach ([
            ['type' => 'تحاليل', 'title' => 'تحليل صورة دم كاملة (CBC)'],
            ['type' => 'سونار', 'title' => 'سونار متابعة الأسبوع 20'],
        ] as $i => $rec) {
            MedicalRecord::create([
                'id' => $this->uuid('8009', $i + 1),
                'patient_id' => $patients['P2024001']->id,
                'record_type' => $rec['type'],
                'title' => $rec['title'],
            ]);
        }

        return $patients;
    }

    private function seedAppointments(array $patients, array $branches, array $services): void
    {
        $rows = [
            ['file' => 'P2024001', 'service' => 'pregnancy', 'time' => '09:00', 'branch' => 'beni-mazar', 'status' => 'confirmed', 'notes' => null],
            ['file' => 'P2024002', 'service' => 'consultation', 'time' => '09:30', 'branch' => 'beni-mazar', 'status' => 'waiting', 'notes' => null],
            ['file' => 'P2024003', 'service' => 'ivf-consultation', 'time' => '10:00', 'branch' => 'beni-mazar', 'status' => 'confirmed', 'notes' => 'مراجعة التحاليل'],
            ['file' => 'P2024004', 'service' => 'followup', 'time' => '10:30', 'branch' => 'sheikh-zayed', 'status' => 'pending', 'notes' => null],
            ['file' => 'P2024005', 'service' => 'sonar-4d', 'time' => '11:00', 'branch' => 'beni-mazar', 'status' => 'cancelled', 'notes' => 'تم الإلغاء بطلب المريضة'],
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
