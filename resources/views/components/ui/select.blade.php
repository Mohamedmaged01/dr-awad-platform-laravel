@props([
    'label' => null,
    'error' => null,
    'options' => [],
    'placeholder' => null,
])
@php
    $selectId = $attributes->get('id') ?? $attributes->get('name');
    $classes = twMerge(
        'w-full px-4 py-3 border rounded-lg transition-all duration-300',
        'bg-white dark:bg-gray-800',
        'border-gray-200 dark:border-gray-700',
        'text-gray-900 dark:text-white',
        'focus:outline-none focus:ring-2 focus:ring-medical-blue focus:border-transparent',
        $error ? 'border-red-500 focus:ring-red-500' : '',
        $attributes->get('class') ?? ''
    );
@endphp
<div class="w-full">
    @if ($label)
        <label for="{{ $selectId }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
            {{ $label }}
            @if ($attributes->has('required'))<span class="text-red-500 mr-1">*</span>@endif
        </label>
    @endif
    <select id="{{ $selectId }}" {{ $attributes->except(['id', 'class']) }} class="{{ $classes }}">
        @if ($placeholder)
            <option value="" disabled selected>{{ $placeholder }}</option>
        @endif
        @foreach ($options as $option)
            <option value="{{ $option['value'] }}">{{ $option['label'] }}</option>
        @endforeach
    </select>
    @if ($error)
        <p class="mt-1 text-sm text-red-500">{{ $error }}</p>
    @endif
</div>
