@php
    $tabs = [
        ['key' => 'blog',     'href' => route('admin.content.blog'),     'label' => __('blog'),        'icon' => 'newspaper'],
        ['key' => 'videos',   'href' => route('admin.content.videos'),   'label' => __('videos'),      'icon' => 'video'],
        ['key' => 'services', 'href' => route('admin.content.services'), 'label' => __('services'),    'icon' => 'stethoscope'],
        ['key' => 'about',    'href' => route('admin.content.about'),    'label' => __('aboutDoctor'), 'icon' => 'user'],
        ['key' => 'contact',  'href' => route('admin.content.contact'),  'label' => __('contact'),     'icon' => 'map-pin'],
    ];
    $active = $active ?? '';
@endphp
<div class="flex flex-wrap items-center gap-2 mb-6">
    @foreach ($tabs as $t)
        <a href="{{ $t['href'] }}"
           @class([
               'inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold transition-colors',
               'bg-medical-blue text-white shadow-sm' => $active === $t['key'],
               'bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 border border-gray-200 dark:border-gray-700' => $active !== $t['key'],
           ])>
            @svg('lucide-' . $t['icon'], 'w-[18px] h-[18px]')
            {{ $t['label'] }}
        </a>
    @endforeach
</div>
