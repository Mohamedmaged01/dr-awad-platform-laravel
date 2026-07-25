@props([
    'color' => 'gray',   // green | yellow | blue | red | gray | purple
    'solid' => false,
])
@php
    $map = [
        'green'  => 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300',
        'yellow' => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-300',
        'blue'   => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300',
        'red'    => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300',
        'purple' => 'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-300',
        'gray'   => 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300',
    ];
    $solidMap = [
        'green'  => 'bg-green-500 text-white',
        'yellow' => 'bg-yellow-500 text-white',
        'blue'   => 'bg-blue-500 text-white',
        'red'    => 'bg-red-500 text-white',
        'purple' => 'bg-purple-500 text-white',
        'gray'   => 'bg-gray-500 text-white',
    ];
    $classes = $solid ? ($solidMap[$color] ?? $solidMap['gray']) : ($map[$color] ?? $map['gray']);
@endphp
<span {{ $attributes->merge(['class' => "inline-flex items-center px-3 py-1 rounded-full text-[11px] font-bold $classes"]) }}>
    {{ $slot }}
</span>
