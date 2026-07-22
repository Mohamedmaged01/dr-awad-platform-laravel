@props([
    'variant' => 'primary',
    'size' => 'md',
    'isLoading' => false,
    'href' => null,
    'leftIcon' => null,
    'rightIcon' => null,
])
@php
    $base = 'inline-flex items-center justify-center font-semibold rounded-lg transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed';

    $variants = [
        'primary' => 'bg-medical-blue text-white hover:bg-medical-blue-dark focus:ring-medical-blue shadow-md hover:shadow-lg',
        'secondary' => 'bg-soft-pink text-medical-blue hover:bg-soft-pink-dark focus:ring-soft-pink',
        'outline' => 'border-2 border-medical-blue text-medical-blue hover:bg-medical-blue hover:text-white focus:ring-medical-blue',
        'ghost' => 'text-medical-blue hover:bg-medical-blue/10 focus:ring-medical-blue',
        'gold' => 'bg-light-gold text-white hover:bg-light-gold-light focus:ring-light-gold shadow-md hover:shadow-lg',
        'danger' => 'bg-red-500 text-white hover:bg-red-600 focus:ring-red-500 shadow-md hover:shadow-lg',
    ];

    $sizes = [
        'sm' => 'px-3 py-1.5 text-sm gap-1.5',
        'md' => 'px-5 py-2.5 text-base gap-2',
        'lg' => 'px-7 py-3.5 text-lg gap-2.5',
    ];

    $classes = twMerge($base, $variants[$variant] ?? $variants['primary'], $sizes[$size] ?? $sizes['md'], $attributes->get('class') ?? '');
    $tag = $href ? 'a' : 'button';
@endphp
<{{ $tag }}
    {{ $attributes->except('class')->merge($href ? ['href' => $href] : []) }}
    class="{{ $classes }}"
    @if ($isLoading && ! $href) disabled @endif
>
    @if ($isLoading)
        <svg class="animate-spin h-5 w-5" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none" />
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" />
        </svg>
    @elseif ($leftIcon)
        <span class="flex-shrink-0">{{ $leftIcon }}</span>
    @endif

    {{ $slot }}

    @if ($rightIcon && ! $isLoading)
        <span class="flex-shrink-0">{{ $rightIcon }}</span>
    @endif
</{{ $tag }}>
