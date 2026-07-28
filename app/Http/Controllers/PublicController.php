<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Service;
use App\Support\ClinicData;

class PublicController extends Controller
{
    public function home()
    {
        return view('home', [
            'homeServices' => ClinicData::homeServices(),
            'aboutAchievements' => ClinicData::aboutAchievements(),
            'aboutHighlights' => ClinicData::aboutHighlights(),
            'testimonials' => ClinicData::testimonials(),
            'bookingBranches' => $this->branchOptions(),
            'bookingServices' => $this->serviceOptions(),
            'faqs' => ClinicData::faqs(),
        ]);
    }

    /** DB-backed, locale-aware branch options for the booking selects. */
    private function branchOptions(): array
    {
        $en = app()->getLocale() === 'en';

        return Branch::where('is_active', true)->orderByDesc('is_main')->get()->map(fn (Branch $b) => [
            'value' => $b->id,
            'label' => $en && $b->name_en ? $b->name_en : $b->name_ar,
            'address' => $en && $b->address_en ? $b->address_en : $b->address_ar,
        ])->all();
    }

    /** DB-backed, locale-aware service options for the booking selects. */
    private function serviceOptions(): array
    {
        $en = app()->getLocale() === 'en';

        return Service::where('is_active', true)->orderBy('name_ar')->get()->map(fn (Service $s) => [
            'value' => $s->id,
            'label' => $en && $s->name_en ? $s->name_en : $s->name_ar,
            'price' => (int) $s->price,
        ])->all();
    }

    public function about()
    {
        return view('about', [
            'qualifications' => ClinicData::qualifications(),
            'experience' => ClinicData::experience(),
            'memberships' => ClinicData::memberships(),
            'whyChooseUs' => ClinicData::whyChooseUs(),
        ]);
    }

    public function services()
    {
        return view('services', ['services' => ClinicData::services()]);
    }

    public function videos()
    {
        return view('videos', [
            'videos' => ClinicData::videos(),
            'categories' => ClinicData::videoCategories(),
        ]);
    }

    public function blog()
    {
        return view('blog', [
            'articles' => ClinicData::articles(),
            'categories' => ClinicData::articleCategories(),
        ]);
    }

    public function contact()
    {
        return view('contact', ['branches' => ClinicData::contactBranches()]);
    }

    public function booking()
    {
        return view('booking', [
            'branches' => $this->branchOptions(),
            'services' => $this->serviceOptions(),
            'timeSlots' => ClinicData::timeSlots(),
        ]);
    }

    public function patientPortal()
    {
        $user = auth()->user();
        $patient = ($user && $user->role === 'patient') ? $user->patient : null;

        $appointments = $patient
            ? $patient->appointments()->with(['service', 'branch'])->orderByDesc('appointment_date')->take(5)->get()->map(fn ($a) => [
                'service' => $a->service?->name_ar ?? '—',
                'branch' => $a->branch?->name_ar ?? '—',
                'date' => optional($a->appointment_date)->isoFormat('D MMMM YYYY'),
                'time' => $a->time_label,
            ])->all()
            : [];

        $records = $patient
            ? $patient->medicalRecords()->latest()->take(6)->get()->map(fn ($r) => [
                'type' => $r->record_type,
                'name' => $r->title ?? $r->record_type,
                'date' => optional($r->created_at)->isoFormat('D MMMM YYYY'),
            ])->all()
            : [];

        return view('patient-portal', [
            'patient' => $patient,
            'appointments' => $appointments,
            'records' => $records,
        ]);
    }

    /** Simple stubs for links the prototype pointed at but never built. */
    public function serviceDetail(string $slug)
    {
        $service = collect(ClinicData::services())->firstWhere('id', $slug);

        return $service
            ? view('service-detail', ['service' => $service])
            : redirect()->route('services');
    }

    public function blogPost(string $id)
    {
        $article = collect(ClinicData::articles())->firstWhere('id', $id);

        return $article
            ? view('blog-post', ['article' => $article])
            : redirect()->route('blog');
    }
}
