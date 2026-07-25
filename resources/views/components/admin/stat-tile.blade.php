@props([
    'label' => '',
    'value' => '',
    'icon' => 'activity',
    'color' => 'bg-blue-500',
    'trend' => null,      // e.g. '+12%'
    'trendUp' => true,
])
{{-- KPI tile — mirrors the admin dashboard stat cards. --}}
<x-ui.card>
    <x-ui.card-content class="p-6">
        <div class="flex items-start justify-between mb-4">
            <div class="w-12 h-12 rounded-xl {{ $color }} flex items-center justify-center text-white">
                @svg('lucide-' . $icon, 'w-6 h-6')
            </div>
            @if ($trend)
                <span class="flex items-center gap-1 text-sm font-bold {{ $trendUp ? 'text-green-500' : 'text-red-500' }}">
                    @svg($trendUp ? 'lucide-trending-up' : 'lucide-trending-down', 'w-4 h-4')
                    {{ $trend }}
                </span>
            @endif
        </div>
        <div class="text-2xl font-bold text-gray-800 dark:text-white">{{ $value }}</div>
        <div class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ $label }}</div>
    </x-ui.card-content>
</x-ui.card>
