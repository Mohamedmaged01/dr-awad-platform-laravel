<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Concerns\HandlesUploads;
use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Content;
use App\Models\Service;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

/**
 * "ادارة المحتوى" — one hub that lets the clinic edit every public page:
 * Blog + Videos (contents table), Services (services table), About (settings),
 * and Contact/branches (branches table). Every media field takes an upload or a URL.
 */
class SiteContentController extends Controller
{
    use HandlesUploads;

    public function hub()
    {
        return redirect()->route('admin.content.blog');
    }

    /* ============================================================ Blog */

    public function blog()
    {
        return view('admin.content.blog', [
            'items' => Content::where('type', 'article')->latest()->get(),
        ]);
    }

    public function storeBlog(Request $request)
    {
        $data = $this->validateBlog($request);
        Content::create($this->blogAttributes($request, $data));

        return back()->with('status', __('saved'));
    }

    public function updateBlog(Request $request, Content $content)
    {
        $data = $this->validateBlog($request);
        $content->update($this->blogAttributes($request, $data, $content));

        return back()->with('status', __('saved'));
    }

    public function destroyBlog(Content $content)
    {
        $content->delete();

        return back()->with('status', __('deleted'));
    }

    private function validateBlog(Request $request): array
    {
        return $request->validate([
            'title_ar' => ['required', 'string', 'max:300'],
            'title_en' => ['nullable', 'string', 'max:300'],
            'category' => ['nullable', 'string', 'max:100'],
            'author_name' => ['nullable', 'string', 'max:100'],
            'read_time' => ['nullable', 'string', 'max:50'],
            'excerpt_ar' => ['nullable', 'string', 'max:1000'],
            'content_ar' => ['nullable', 'string'],
            'image_url' => ['nullable', 'string', 'max:500'],
            'image_file' => ['nullable', 'image', 'max:8192'],
        ]);
    }

    private function blogAttributes(Request $request, array $data, ?Content $existing = null): array
    {
        return [
            'type' => 'article',
            'title_ar' => $data['title_ar'],
            'title_en' => $data['title_en'] ?? null,
            'excerpt_ar' => $data['excerpt_ar'] ?? null,
            'content_ar' => $data['content_ar'] ?? null,
            'image_url' => $this->resolveMedia($request, 'image_file', 'image_url', 'blog', $existing?->image_url),
            'meta' => [
                'category' => $data['category'] ?? null,
                'author_name' => $data['author_name'] ?? null,
                'read_time' => $data['read_time'] ?? null,
            ],
            'is_published' => $request->boolean('is_published'),
            'published_at' => $request->boolean('is_published') ? ($existing?->published_at ?? now()) : null,
        ];
    }

    /* ============================================================ Videos */

    public function videos()
    {
        return view('admin.content.videos', [
            'items' => Content::where('type', 'video')->latest()->get(),
        ]);
    }

    public function storeVideo(Request $request)
    {
        $data = $this->validateVideo($request);
        Content::create($this->videoAttributes($request, $data));

        return back()->with('status', __('saved'));
    }

    public function updateVideo(Request $request, Content $content)
    {
        $data = $this->validateVideo($request);
        $content->update($this->videoAttributes($request, $data, $content));

        return back()->with('status', __('saved'));
    }

    public function destroyVideo(Content $content)
    {
        $content->delete();

        return back()->with('status', __('deleted'));
    }

    private function validateVideo(Request $request): array
    {
        return $request->validate([
            'title_ar' => ['required', 'string', 'max:300'],
            'title_en' => ['nullable', 'string', 'max:300'],
            'category' => ['nullable', 'string', 'max:100'],
            'duration' => ['nullable', 'string', 'max:20'],
            'views_label' => ['nullable', 'string', 'max:20'],
            'excerpt_ar' => ['nullable', 'string', 'max:1000'],
            'video_url' => ['nullable', 'string', 'max:500'],
            'video_file' => ['nullable', 'mimetypes:video/mp4,video/webm,video/ogg', 'max:51200'],
            'image_url' => ['nullable', 'string', 'max:500'],
            'image_file' => ['nullable', 'image', 'max:8192'],
        ]);
    }

    private function videoAttributes(Request $request, array $data, ?Content $existing = null): array
    {
        // A file upload wins; otherwise store the YouTube id (normalised) or raw URL.
        $video = $existing?->video_url;
        if ($request->hasFile('video_file')) {
            $video = $this->resolveMedia($request, 'video_file', 'video_url', 'videos', $existing?->video_url);
        } elseif (trim((string) $request->input('video_url')) !== '') {
            $video = $this->youtubeId($request->input('video_url'));
        }

        return [
            'type' => 'video',
            'title_ar' => $data['title_ar'],
            'title_en' => $data['title_en'] ?? null,
            'excerpt_ar' => $data['excerpt_ar'] ?? null,
            'video_url' => $video,
            'image_url' => $this->resolveMedia($request, 'image_file', 'image_url', 'videos', $existing?->image_url),
            'meta' => [
                'category' => $data['category'] ?? null,
                'duration' => $data['duration'] ?? null,
                'views_label' => $data['views_label'] ?? null,
            ],
            'is_published' => $request->boolean('is_published'),
            'published_at' => $request->boolean('is_published') ? ($existing?->published_at ?? now()) : null,
        ];
    }

    /* ============================================================ Services */

    public function services()
    {
        return view('admin.content.services', [
            'items' => Service::orderBy('sort_order')->orderBy('name_ar')->get(),
        ]);
    }

    public function storeService(Request $request)
    {
        $data = $this->validateService($request);
        Service::create($this->serviceAttributes($request, $data));

        return back()->with('status', __('saved'));
    }

    public function updateService(Request $request, Service $service)
    {
        $data = $this->validateService($request);
        $service->update($this->serviceAttributes($request, $data, $service));

        return back()->with('status', __('saved'));
    }

    public function destroyService(Service $service)
    {
        $service->delete();

        return back()->with('status', __('deleted'));
    }

    private function validateService(Request $request): array
    {
        return $request->validate([
            'name_ar' => ['required', 'string', 'max:200'],
            'name_en' => ['nullable', 'string', 'max:200'],
            'description_ar' => ['nullable', 'string'],
            'description_en' => ['nullable', 'string'],
            'features' => ['nullable', 'string'],
            'icon' => ['nullable', 'string', 'max:100'],
            'color' => ['nullable', 'string', 'max:100'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'image_url' => ['nullable', 'string', 'max:500'],
            'image_file' => ['nullable', 'image', 'max:8192'],
        ]);
    }

    private function serviceAttributes(Request $request, array $data, ?Service $existing = null): array
    {
        // Features are entered one per line.
        $features = collect(preg_split('/\r\n|\r|\n/', (string) ($data['features'] ?? '')))
            ->map(fn ($l) => trim($l))->filter()->values()->all();

        return [
            'name_ar' => $data['name_ar'],
            'name_en' => $data['name_en'] ?? null,
            'description_ar' => $data['description_ar'] ?? null,
            'description_en' => $data['description_en'] ?? null,
            'features' => $features,
            'icon' => $data['icon'] ?: 'stethoscope',
            'color' => $data['color'] ?: 'from-medical-blue to-cyan-500',
            'price' => $data['price'] ?? null,
            'sort_order' => $data['sort_order'] ?? 0,
            'image_url' => $this->resolveMedia($request, 'image_file', 'image_url', 'services', $existing?->image_url),
            'slug' => $existing?->slug ?? Str::slug($data['name_en'] ?: $data['name_ar']) . '-' . Str::lower(Str::random(4)),
            'is_active' => $request->boolean('is_active'),
            'is_featured' => $request->boolean('is_featured'),
        ];
    }

    /* ============================================================ About */

    public function about()
    {
        return view('admin.content.about', [
            'bioAr' => Setting::get('about_bio_ar', ''),
            'bioEn' => Setting::get('about_bio_en', ''),
            'doctorImage' => Setting::get('about_doctor_image', ''),
            'qualifications' => $this->readList('about_qualifications'),
            'experience' => $this->readList('about_experience'),
            'memberships' => $this->readList('about_memberships'),
            'why' => $this->readList('about_why'),
        ]);
    }

    public function updateAbout(Request $request)
    {
        $data = $request->validate([
            'bio_ar' => ['nullable', 'string'],
            'bio_en' => ['nullable', 'string'],
            'doctor_image_url' => ['nullable', 'string', 'max:500'],
            'doctor_image_file' => ['nullable', 'image', 'max:8192'],
            'qualifications' => ['nullable', 'array'],
            'experience' => ['nullable', 'array'],
            'memberships' => ['nullable', 'array'],
            'why' => ['nullable', 'array'],
        ]);

        Setting::put('about_bio_ar', $data['bio_ar'] ?? null, 'about');
        Setting::put('about_bio_en', $data['bio_en'] ?? null, 'about');
        Setting::put(
            'about_doctor_image',
            $this->resolveMedia($request, 'doctor_image_file', 'doctor_image_url', 'about', Setting::get('about_doctor_image')),
            'about'
        );

        // Bilingual lists: keep only rows that have an Arabic value.
        $this->writeList('about_qualifications', $request->input('qualifications', []), ['ar', 'en']);
        $this->writeList('about_experience', $request->input('experience', []), ['ar', 'en']);
        $this->writeList('about_memberships', $request->input('memberships', []), ['ar', 'en']);
        $this->writeList('about_why', $request->input('why', []), ['icon', 'title_ar', 'title_en', 'desc_ar', 'desc_en'], 'title_ar');

        return back()->with('status', __('saved'));
    }

    private function readList(string $key): array
    {
        return json_decode(Setting::get($key, '[]'), true) ?: [];
    }

    private function writeList(string $key, array $rows, array $fields, string $requiredField = 'ar'): void
    {
        $clean = collect($rows)
            ->filter(fn ($row) => is_array($row) && trim((string) ($row[$requiredField] ?? '')) !== '')
            ->map(fn ($row) => collect($fields)->mapWithKeys(fn ($f) => [$f => trim((string) ($row[$f] ?? ''))])->all())
            ->values()->all();

        Setting::put($key, json_encode($clean, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), 'about');
    }

    /* ============================================================ Contact / branches */

    public function contact()
    {
        return view('admin.content.contact', [
            'branches' => Branch::orderByDesc('is_main')->get(),
            'introAr' => Setting::get('contact_intro_ar', ''),
            'introEn' => Setting::get('contact_intro_en', ''),
        ]);
    }

    public function updateContactIntro(Request $request)
    {
        $data = $request->validate([
            'intro_ar' => ['nullable', 'string', 'max:1000'],
            'intro_en' => ['nullable', 'string', 'max:1000'],
        ]);

        Setting::put('contact_intro_ar', $data['intro_ar'] ?? null, 'contact');
        Setting::put('contact_intro_en', $data['intro_en'] ?? null, 'contact');

        return back()->with('status', __('saved'));
    }

    public function storeBranch(Request $request)
    {
        $data = $this->validateBranch($request);
        Branch::create($this->branchAttributes($data) + ['is_active' => true]);

        return back()->with('status', __('saved'));
    }

    public function updateBranch(Request $request, Branch $branch)
    {
        $data = $this->validateBranch($request);
        $branch->update($this->branchAttributes($data, $branch));

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
            'name_ar' => ['required', 'string', 'max:200'],
            'name_en' => ['nullable', 'string', 'max:200'],
            'address_ar' => ['required', 'string', 'max:500'],
            'address_en' => ['nullable', 'string', 'max:500'],
            'phone' => ['required', 'string', 'max:20'],
            'whatsapp' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:255'],
            'google_maps_url' => ['nullable', 'string', 'max:500'],
            'hours' => ['nullable', 'string', 'max:100'],
        ]);
    }

    private function branchAttributes(array $data, ?Branch $branch = null): array
    {
        return [
            'name_ar' => $data['name_ar'],
            'name_en' => $data['name_en'] ?? null,
            'address_ar' => $data['address_ar'],
            'address_en' => $data['address_en'] ?? null,
            'phone' => $data['phone'],
            'whatsapp' => $data['whatsapp'] ?? $data['phone'],
            'email' => $data['email'] ?? null,
            'google_maps_url' => $data['google_maps_url'] ?? null,
            'working_hours' => array_merge($branch?->working_hours ?? [], [
                'display' => $data['hours'] ?? ($branch?->working_hours['display'] ?? ''),
                'short' => $data['name_ar'],
            ]),
        ];
    }
}
