@extends('layouts.admin')

@php
    $statusConfig = [
        'confirmed' => ['label' => 'مؤكد', 'color' => 'bg-green-100 text-green-700', 'icon' => 'check-circle'],
        'waiting' => ['label' => 'في الانتظار', 'color' => 'bg-yellow-100 text-yellow-700', 'icon' => 'clock'],
        'pending' => ['label' => 'معلق', 'color' => 'bg-blue-100 text-blue-700', 'icon' => 'alert-circle'],
        'cancelled' => ['label' => 'ملغي', 'color' => 'bg-red-100 text-red-700', 'icon' => 'x-circle'],
        'completed' => ['label' => 'مكتمل', 'color' => 'bg-gray-100 text-gray-700', 'icon' => 'check-circle'],
    ];
    $today = $appointments->where('appointment_date', '2024-01-20')->count();
@endphp

@section('content')
    <div class="space-y-6" x-data="{ view: 'list', selectedDate: '{{ now()->toDateString() }}' }">
        {{-- Header --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-800 dark:text-white">إدارة المواعيد</h1>
                <p class="text-gray-500">مواعيد اليوم: {{ $today }}</p>
            </div>
            <div class="flex gap-3">
                <div class="flex rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <button @click="view = 'list'" class="px-4 py-2 text-sm" :class="view === 'list' ? 'bg-medical-blue text-white' : 'bg-white dark:bg-gray-800'">قائمة</button>
                    <button @click="view = 'calendar'" class="px-4 py-2 text-sm" :class="view === 'calendar' ? 'bg-medical-blue text-white' : 'bg-white dark:bg-gray-800'">تقويم</button>
                </div>
                <x-ui.button variant="primary">
                    <x-slot:leftIcon>@svg('lucide-plus', 'w-[18px] h-[18px]')</x-slot:leftIcon>
                    حجز جديد
                </x-ui.button>
            </div>
        </div>

        {{-- Filters --}}
        <x-ui.card>
            <x-ui.card-content class="p-4">
                <div class="flex flex-col md:flex-row gap-4">
                    <div class="flex-1">
                        <x-ui.input placeholder="بحث بالاسم أو الهاتف...">
                            <x-slot:leftIcon>@svg('lucide-search', 'w-[18px] h-[18px]')</x-slot:leftIcon>
                        </x-ui.input>
                    </div>
                    <x-ui.input type="date" x-model="selectedDate" class="w-auto" />
                    <x-ui.button variant="outline">
                        <x-slot:leftIcon>@svg('lucide-filter', 'w-[18px] h-[18px]')</x-slot:leftIcon>
                        تصفية
                    </x-ui.button>
                </div>
            </x-ui.card-content>
        </x-ui.card>

        {{-- List View --}}
        <div x-show="view === 'list'">
            <x-ui.card>
                <x-ui.card-content class="p-0">
                    <div class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach ($appointments as $appointment)
                            @php $info = $statusConfig[$appointment->status] ?? $statusConfig['pending']; @endphp
                            <div class="p-4 hover:bg-gray-50 dark:hover:bg-gray-800 flex items-center justify-between">
                                <div class="flex items-center gap-4">
                                    <div class="text-center min-w-[60px]">
                                        <div class="text-2xl font-bold text-medical-blue">{{ $appointment->time_label }}</div>
                                    </div>
                                    <div class="w-px h-12 bg-gray-200 dark:bg-gray-700"></div>
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <h3 class="font-medium text-gray-800 dark:text-white">{{ $appointment->patient->name }}</h3>
                                            <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $info['color'] }}">
                                                @svg('lucide-' . $info['icon'], 'inline w-3 h-3 mr-1')
                                                {{ $info['label'] }}
                                            </span>
                                        </div>
                                        <div class="flex items-center gap-4 mt-1 text-sm text-gray-500">
                                            <span>{{ $appointment->service?->name_ar }}</span>
                                            <span>•</span>
                                            <span>{{ $appointment->branch->short_name }}</span>
                                            <span>•</span>
                                            <span>{{ $appointment->patient->phone }}</span>
                                        </div>
                                        @if ($appointment->notes)
                                            <p class="text-sm text-gray-400 mt-1">📝 {{ $appointment->notes }}</p>
                                        @endif
                                    </div>
                                </div>
                                <div class="flex items-center gap-2">
                                    @if ($appointment->status === 'pending')
                                        <x-ui.button variant="outline" size="sm">تأكيد</x-ui.button>
                                        <x-ui.button variant="ghost" size="sm" class="text-red-500">إلغاء</x-ui.button>
                                    @elseif ($appointment->status === 'confirmed')
                                        <x-ui.button variant="primary" size="sm">بدء الكشف</x-ui.button>
                                    @endif
                                    <button class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg">
                                        @svg('lucide-more-vertical', 'w-[18px] h-[18px] text-gray-500')
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </x-ui.card-content>
            </x-ui.card>
        </div>

        {{-- Calendar View --}}
        <div x-show="view === 'calendar'" x-cloak>
            <x-ui.card>
                <x-ui.card-header class="flex flex-row items-center justify-between">
                    <button class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg">@svg('lucide-chevron-right', 'w-5 h-5')</button>
                    <h3 class="text-lg font-bold text-gray-800 dark:text-white">يناير 2024</h3>
                    <button class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg">@svg('lucide-chevron-left', 'w-5 h-5')</button>
                </x-ui.card-header>
                <x-ui.card-content>
                    <div class="grid grid-cols-7 gap-1">
                        @foreach (['سبت', 'أحد', 'اثنين', 'ثلاثاء', 'أربعاء', 'خميس', 'جمعة'] as $day)
                            <div class="text-center text-sm font-medium text-gray-500 py-2">{{ $day }}</div>
                        @endforeach
                        @for ($i = 0; $i < 35; $i++)
                            @php
                                $day = $i - 5;
                                $isCurrentMonth = $day >= 1 && $day <= 31;
                                $isToday = $day === 20;
                                $hasAppointments = in_array($day, [15, 18, 20, 22, 25, 28]);
                            @endphp
                            <div @class([
                                'aspect-square p-2 rounded-lg',
                                'text-gray-300' => ! $isCurrentMonth,
                                'bg-medical-blue text-white' => $isToday,
                                'bg-blue-50 dark:bg-blue-900/20' => $hasAppointments && $isCurrentMonth && ! $isToday,
                                'hover:bg-gray-100 dark:hover:bg-gray-700 cursor-pointer' => $isCurrentMonth,
                            ])>
                                @if ($isCurrentMonth)
                                    <div class="text-center">
                                        <div class="text-sm font-medium">{{ $day }}</div>
                                        @if ($hasAppointments && ! $isToday)
                                            <div class="w-1.5 h-1.5 bg-medical-blue rounded-full mx-auto mt-1"></div>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        @endfor
                    </div>
                </x-ui.card-content>
            </x-ui.card>
        </div>

        {{-- Stats --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            @foreach ($stats as $stat)
                <x-ui.card>
                    <x-ui.card-content class="p-4 flex items-center gap-4">
                        <div class="w-3 h-12 rounded-full {{ $stat['color'] }}"></div>
                        <div>
                            <p class="text-2xl font-bold text-gray-800 dark:text-white">{{ $stat['value'] }}</p>
                            <p class="text-sm text-gray-500">{{ $stat['label'] }}</p>
                        </div>
                    </x-ui.card-content>
                </x-ui.card>
            @endforeach
        </div>
    </div>
@endsection
