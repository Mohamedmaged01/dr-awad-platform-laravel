@php
    $classes = twMerge('p-6', $attributes->get('class') ?? '');
@endphp
<div {{ $attributes->except('class') }} class="{{ $classes }}">
    {{ $slot }}
</div>
