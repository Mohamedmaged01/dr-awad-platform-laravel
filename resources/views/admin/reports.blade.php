@extends('layouts.admin')

@php
    $max = max($monthly) ?: 1;
    // Build a conic-gradient for the donut from the distribution percentages.
    $hex = ['bg-medical-blue' => '#1e5f9f', 'bg-light-gold' => '#d4af37', 'bg-pink-500' => '#ec4899', 'bg-emerald-500' => '#10b981'];
    $stops = [];
    $acc = 0;
    foreach ($distribution as $d) {
        $c = $hex[$d['color']] ?? '#9ca3af';
        $stops[] = "$c {$acc}% " . ($acc + $d['percent']) . '%';
        $acc += $d['percent'];
    }
    $conic = 'conic-gradient(' . implode(', ', $stops) . ')';
@endphp

@section('content')
<div>
    <x-admin.page-header :title="__('reports')">
        <x-slot:actions>
            <x-ui.button variant="gold" size="sm">
                <x-slot:leftIcon>@svg('lucide-sparkles', 'w-[18px] h-[18px]')</x-slot:leftIcon>
                {{ __('ai_insights') }}
            </x-ui.button>
        </x-slot:actions>
    </x-admin.page-header>

    {{-- KPIs --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        @foreach ($kpis as $k)
            <x-admin.stat-tile :label="$k['label']" :value="$k['value']" :icon="$k['icon']" :color="$k['color']" :trend="$k['change']" :trend-up="$k['up']" />
        @endforeach
    </div>

    <div class="grid lg:grid-cols-3 gap-6 mb-6">
        {{-- Bar chart --}}
        <x-ui.card class="lg:col-span-2">
            <x-ui.card-header>
                <h2 class="font-bold text-gray-800 dark:text-white">{{ __('activity_analysis') }}</h2>
            </x-ui.card-header>
            <x-ui.card-content>
                <div class="flex items-end justify-between gap-2 h-48">
                    @foreach ($monthly as $v)
                        <div class="flex-1 flex flex-col items-center justify-end h-full group">
                            <div class="w-full bg-medical-blue/15 rounded-t-md relative flex items-end" style="height: 100%">
                                <div class="w-full bg-medical-blue rounded-t-md transition-all group-hover:bg-medical-blue-dark" style="height: {{ round($v / $max * 100) }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </x-ui.card-content>
        </x-ui.card>

        {{-- Donut --}}
        <x-ui.card>
            <x-ui.card-header>
                <h2 class="font-bold text-gray-800 dark:text-white">{{ __('services') }}</h2>
            </x-ui.card-header>
            <x-ui.card-content>
                <div class="flex justify-center mb-4">
                    <div class="relative w-40 h-40 rounded-full" style="background: {{ $conic }}">
                        <div class="absolute inset-6 bg-white dark:bg-gray-800 rounded-full flex items-center justify-center">
                            <span class="text-sm font-bold text-gray-800 dark:text-white">100%</span>
                        </div>
                    </div>
                </div>
                <div class="space-y-2">
                    @foreach ($distribution as $d)
                        <div class="flex items-center justify-between text-sm">
                            <div class="flex items-center gap-2">
                                <span class="w-3 h-3 rounded-full {{ $d['color'] }}"></span>
                                <span class="text-gray-600 dark:text-gray-400">{{ $d['label'] }}</span>
                            </div>
                            <span class="font-medium text-gray-800 dark:text-white">{{ $d['percent'] }}%</span>
                        </div>
                    @endforeach
                </div>
            </x-ui.card-content>
        </x-ui.card>
    </div>

    {{-- AI insights --}}
    <x-ui.card class="bg-gradient-to-r from-medical-blue to-medical-blue-dark text-white">
        <x-ui.card-content class="p-6">
            <div class="flex items-center gap-3 mb-4">
                @svg('lucide-brain-circuit', 'w-6 h-6 text-light-gold')
                <h2 class="font-bold text-lg">{{ __('ai_recommendation') }}</h2>
            </div>
            <div class="grid md:grid-cols-2 gap-4">
                <div class="bg-white/10 rounded-xl p-4">
                    <p class="font-semibold mb-1">{{ __('peak_prediction') }}</p>
                    <p class="text-sm text-white/80">{{ app()->getLocale() === 'en' ? 'Appointment demand peaks on Sundays and Mondays; consider adding staff on those days.' : 'يبلغ الطلب على المواعيد ذروته أيام الأحد والاثنين؛ يُنصح بزيادة الطاقم في هذين اليومين.' }}</p>
                </div>
                <div class="bg-white/10 rounded-xl p-4">
                    <p class="font-semibold mb-1">{{ __('success_rate') }}</p>
                    <p class="text-sm text-white/80">{{ app()->getLocale() === 'en' ? 'IVF success rate improved 5% this quarter compared to the previous one.' : 'تحسّنت نسبة نجاح الحقن المجهري بمقدار 5% هذا الربع مقارنة بالربع السابق.' }}</p>
                </div>
            </div>
        </x-ui.card-content>
    </x-ui.card>
</div>
@endsection
