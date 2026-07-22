@php
    $classes = twMerge('px-6 py-4 border-t border-gray-100 dark:border-gray-700', $attributes->get('class') ?? '');
@endphp
<div {{ $attributes->except('class') }} class="{{ $classes }}">
    {{ $slot }}
</div>
