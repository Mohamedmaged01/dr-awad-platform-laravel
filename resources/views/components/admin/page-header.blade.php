@props([
    'title' => '',
    'subtitle' => null,
])
{{-- Standard admin page header: title + optional subtitle on the start side, actions slot on the end. --}}
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-800 dark:text-white">{{ $title }}</h1>
        @if ($subtitle)
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ $subtitle }}</p>
        @endif
    </div>
    @isset($actions)
        <div class="flex items-center gap-3">
            {{ $actions }}
        </div>
    @endisset
</div>
