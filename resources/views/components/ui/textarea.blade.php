@props([
    'label' => null,
    'error' => null,
    'hint' => null,
])
@php
    $textareaId = $attributes->get('id') ?? $attributes->get('name');
    $classes = twMerge(
        'w-full px-4 py-3 border rounded-lg transition-all duration-300',
        'bg-white dark:bg-gray-800',
        'border-gray-200 dark:border-gray-700',
        'text-gray-900 dark:text-white',
        'placeholder:text-gray-400 dark:placeholder:text-gray-500',
        'focus:outline-none focus:ring-2 focus:ring-medical-blue focus:border-transparent',
        'resize-none',
        $error ? 'border-red-500 focus:ring-red-500' : '',
        $attributes->get('class') ?? ''
    );
@endphp
<div class="w-full">
    @if ($label)
        <label for="{{ $textareaId }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
            {{ $label }}
            @if ($attributes->has('required'))<span class="text-red-500 mr-1">*</span>@endif
        </label>
    @endif
    <textarea id="{{ $textareaId }}" {{ $attributes->except(['id', 'class']) }} class="{{ $classes }}">{{ $slot }}</textarea>
    @if ($hint && ! $error)
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $hint }}</p>
    @endif
    @if ($error)
        <p class="mt-1 text-sm text-red-500">{{ $error }}</p>
    @endif
</div>
