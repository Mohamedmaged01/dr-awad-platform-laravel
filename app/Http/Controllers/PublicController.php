<?php

namespace App\Http\Controllers;

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
            'bookingBranches' => ClinicData::bookingSectionBranches(),
            'bookingServices' => ClinicData::bookingSectionServices(),
            'faqs' => ClinicData::faqs(),
        ]);
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
            'branches' => ClinicData::bookingBranches(),
            'services' => ClinicData::bookingServices(),
            'timeSlots' => ClinicData::timeSlots(),
        ]);
    }

    public function patientPortal()
    {
        return view('patient-portal', [
            'appointments' => ClinicData::portalAppointments(),
            'records' => ClinicData::portalRecords(),
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
