@props(['hover' => false, 'glass' => false])
@php
    $classes = twMerge(
        'bg-white dark:bg-gray-800 rounded-2xl shadow-md',
        $hover ? 'card-hover' : '',
        $glass ? 'glass' : '',
        $attributes->get('class') ?? ''
    );
@endphp
<div {{ $attributes->except('class') }} class="{{ $classes }}">
    {{ $slot }}
</div>
