<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Content;
use App\Models\Service;
use App\Models\Setting;
use App\Support\ClinicData;
use Illuminate\Support\Facades\App;

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

        // Booking = the priced line-items; the showcase categories (featured, no price)
        // live on the services page instead.
        return Service::where('is_active', true)->where('price', '>', 0)->orderBy('name_ar')->get()->map(fn (Service $s) => [
            'value' => $s->id,
            'label' => $en && $s->name_en ? $s->name_en : $s->name_ar,
            'price' => (int) $s->price,
        ])->all();
    }

    public function about()
    {
        $en = App::getLocale() === 'en';

        return view('about', [
            'qualifications' => $this->aboutList('about_qualifications', $en) ?: ClinicData::qualifications(),
            'experience' => $this->aboutList('about_experience', $en) ?: ClinicData::experience(),
            'memberships' => $this->aboutList('about_memberships', $en) ?: ClinicData::memberships(),
            'whyChooseUs' => $this->aboutWhy($en) ?: ClinicData::whyChooseUs(),
            'bio' => ($en ? Setting::get('about_bio_en') : Setting::get('about_bio_ar')) ?: __('aboutHeroDesc'),
            'doctorImage' => Setting::get('about_doctor_image') ?: '/images/dr-mohamed-awad.jpg',
        ]);
    }

    /** Decode a bilingual about-list setting into locale strings. */
    private function aboutList(string $key, bool $en): array
    {
        return collect(json_decode(Setting::get($key, '[]'), true) ?: [])
            ->map(fn ($r) => $en && ! empty($r['en']) ? $r['en'] : ($r['ar'] ?? ''))
            ->filter()->values()->all();
    }

    private function aboutWhy(bool $en): array
    {
        return collect(json_decode(Setting::get('about_why', '[]'), true) ?: [])
            ->map(fn ($r) => [
                'icon' => $r['icon'] ?? 'award',
                'title' => $en && ! empty($r['title_en']) ? $r['title_en'] : ($r['title_ar'] ?? ''),
                'desc' => $en && ! empty($r['desc_en']) ? $r['desc_en'] : ($r['desc_ar'] ?? ''),
            ])->filter(fn ($r) => $r['title'] !== '')->values()->all();
    }

    public function services()
    {
        $en = App::getLocale() === 'en';

        $services = Service::where('is_active', true)->where('is_featured', true)
            ->orderBy('sort_order')->orderBy('name_ar')->get()
            ->map(fn (Service $s) => [
                'id' => $s->slug ?: $s->id,
                'icon' => $s->icon ?: 'stethoscope',
                'color' => $s->color ?: 'from-medical-blue to-cyan-500',
                'title' => $en && $s->name_en ? $s->name_en : $s->name_ar,
                'description' => $en && $s->description_en ? $s->description_en : $s->description_ar,
                'features' => (array) ($s->features ?? []),
                'image' => $s->image_url ?: '/images/logo.png',
            ])->all();

        return view('services', ['services' => $services ?: ClinicData::services()]);
    }

    public function videos()
    {
        $videos = Content::where('type', 'video')->where('is_published', true)->latest()->get()
            ->map(fn (Content $c) => $this->mapVideo($c))->all();

        return view('videos', [
            'videos' => $videos ?: $this->fallbackVideos(),
            'categories' => ClinicData::videoCategories(),
        ]);
    }

    private function mapVideo(Content $c): array
    {
        $en = App::getLocale() === 'en';
        $src = (string) $c->video_url;
        $isYoutube = (bool) preg_match('~^[A-Za-z0-9_-]{11}$~', $src);

        return [
            'id' => $c->id,
            'youtube' => $isYoutube ? $src : null,
            'file' => (! $isYoutube && $src !== '') ? $src : null,
            'title' => $en && $c->title_en ? $c->title_en : $c->title_ar,
            'description' => $c->excerpt_ar,
            'category' => $c->meta['category'] ?? '',
            'duration' => $c->meta['duration'] ?? '',
            'views' => $c->meta['views_label'] ?? '',
            'date' => optional($c->published_at ?? $c->created_at)->format('Y-m-d'),
            'thumbnail' => $c->image_url,
        ];
    }

    /** ClinicData videos mapped into the DB video shape (all YouTube). */
    private function fallbackVideos(): array
    {
        return collect(ClinicData::videos())->map(fn ($v) => [
            'id' => $v['id'],
            'youtube' => $v['id'],
            'file' => null,
            'title' => $v['title'],
            'description' => $v['description'],
            'category' => $v['category'],
            'duration' => $v['duration'],
            'views' => $v['views'],
            'date' => $v['date'],
            'thumbnail' => null,
        ])->all();
    }

    public function blog()
    {
        $articles = Content::where('type', 'article')->where('is_published', true)->latest()->get()
            ->map(fn (Content $c) => $this->mapArticle($c))->all();

        return view('blog', [
            'articles' => $articles ?: ClinicData::articles(),
            'categories' => ClinicData::articleCategories(),
        ]);
    }

    private function mapArticle(Content $c): array
    {
        $en = App::getLocale() === 'en';

        return [
            'id' => $c->id,
            'title' => $en && $c->title_en ? $c->title_en : $c->title_ar,
            'excerpt' => $c->excerpt_ar,
            'category' => $c->meta['category'] ?? '',
            'author' => $c->meta['author_name'] ?? 'د. محمد عوض',
            'readTime' => $c->meta['read_time'] ?? '',
            'date' => optional($c->published_at ?? $c->created_at)->format('Y-m-d'),
            'image' => $c->image_url,
            'content' => $c->content_ar,
        ];
    }

    public function contact()
    {
        $en = App::getLocale() === 'en';

        $branches = Branch::where('is_active', true)->orderByDesc('is_main')->get()
            ->map(fn (Branch $b) => [
                'name' => $en && $b->name_en ? $b->name_en : $b->name_ar,
                'address' => $en && $b->address_en ? $b->address_en : $b->address_ar,
                'phone' => $b->phone,
                'whatsapp' => $b->whatsapp ?: $b->phone,
                'email' => $b->email ?: 'info@dr-awad.com',
                'hours' => $b->working_hours['display'] ?? '',
                'mapUrl' => $b->google_maps_url ?: '#',
            ])->all();

        return view('contact', ['branches' => $branches ?: ClinicData::contactBranches()]);
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

    public function serviceDetail(string $slug)
    {
        $en = App::getLocale() === 'en';

        $service = Service::where('is_active', true)
            ->where(function ($q) use ($slug) {
                $q->where('slug', $slug);
                if (\Illuminate\Support\Str::isUuid($slug)) {
                    $q->orWhere('id', $slug);
                }
            })
            ->first();
        if ($service) {
            return view('service-detail', ['service' => [
                'id' => $service->slug ?: $service->id,
                'icon' => $service->icon ?: 'stethoscope',
                'color' => $service->color ?: 'from-medical-blue to-cyan-500',
                'title' => $en && $service->name_en ? $service->name_en : $service->name_ar,
                'description' => $en && $service->description_en ? $service->description_en : $service->description_ar,
                'features' => (array) ($service->features ?? []),
                'image' => $service->image_url ?: '/images/logo.png',
            ]]);
        }

        $fallback = collect(ClinicData::services())->firstWhere('id', $slug);

        return $fallback
            ? view('service-detail', ['service' => $fallback])
            : redirect()->route('services');
    }

    public function blogPost(string $id)
    {
        $content = Content::where('type', 'article')->where('is_published', true)->find($id);
        if ($content) {
            return view('blog-post', ['article' => $this->mapArticle($content)]);
        }

        $article = collect(ClinicData::articles())->firstWhere('id', $id);

        return $article
            ? view('blog-post', ['article' => $article])
            : redirect()->route('blog');
    }
}
