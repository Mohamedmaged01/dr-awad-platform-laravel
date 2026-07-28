@extends('layouts.admin')

@section('content')
    <div class="space-y-6">
        {{-- Welcome --}}
        <div class="bg-gradient-to-r from-medical-blue to-medical-blue-dark rounded-2xl p-6 text-white">
            <h1 class="text-2xl font-bold mb-2">مرحباً بك في لوحة التحكم</h1>
            <p class="text-white/80">هذا ملخص نشاط العيادة اليوم</p>
        </div>

        {{-- Stats Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach ($stats as $stat)
                <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-md">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 rounded-xl {{ $stat['color'] }} flex items-center justify-center">
                            @svg('lucide-' . $stat['icon'], 'w-6 h-6 text-white')
                        </div>
                        @if (! empty($stat['change']))
                            <div class="flex items-center gap-1 text-sm {{ ($stat['trend'] ?? 'up') === 'up' ? 'text-green-500' : 'text-red-500' }}">
                                @svg('lucide-' . (($stat['trend'] ?? 'up') === 'up' ? 'trending-up' : 'trending-down'), 'w-4 h-4')
                                {{ $stat['change'] }}
                            </div>
                        @endif
                    </div>
                    <h3 class="text-gray-500 dark:text-gray-400 text-sm mb-1">{{ $stat['title'] }}</h3>
                    <p class="text-2xl font-bold text-gray-800 dark:text-white">
                        {{ $stat['value'] }}
                        @isset($stat['currency'])<span class="text-sm font-normal mr-1">{{ $stat['currency'] }}</span>@endisset
                    </p>
                </div>
            @endforeach
        </div>

        {{-- Main Grid --}}
        <div class="grid lg:grid-cols-3 gap-6">
            {{-- Today's Appointments --}}
            <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-xl shadow-md">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
                    <h2 class="text-lg font-bold text-gray-800 dark:text-white">مواعيد اليوم</h2>
                    <a href="/admin/appointments" class="text-medical-blue text-sm hover:underline">عرض الكل</a>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        @foreach ($recentAppointments as $appointment)
                            <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-xl">
                                <div class="flex items-center gap-4">
                                    <div class="w-10 h-10 rounded-full bg-medical-blue/10 flex items-center justify-center text-medical-blue font-bold">
                                        {{ mb_substr($appointment['name'], 0, 1) }}
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-800 dark:text-white">{{ $appointment['name'] }}</p>
                                        <p class="text-sm text-gray-500">{{ $appointment['service'] }}</p>
                                    </div>
                                </div>
                                <div class="text-left">
                                    <p class="text-sm font-medium text-gray-800 dark:text-white">{{ $appointment['time'] }}</p>
                                    <span @class([
                                        'text-xs px-2 py-1 rounded-full',
                                        'bg-green-100 text-green-700' => $appointment['status'] === 'confirmed',
                                        'bg-yellow-100 text-yellow-700' => $appointment['status'] === 'waiting',
                                        'bg-gray-100 text-gray-700' => ! in_array($appointment['status'], ['confirmed', 'waiting']),
                                    ])>
                                        @switch($appointment['status'])
                                            @case('confirmed') مؤكد @break
                                            @case('waiting') في الانتظار @break
                                            @default معلق
                                        @endswitch
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- IVF Center Stats --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-lg font-bold text-gray-800 dark:text-white">مركز الحقن المجهري</h2>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        @foreach ($ivfStats as $stat)
                            <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-xl">
                                <div class="flex items-center gap-3">
                                    @svg('lucide-' . $stat['icon'], 'w-5 h-5 ' . $stat['color'])
                                    <span class="text-gray-600 dark:text-gray-300">{{ $stat['label'] }}</span>
                                </div>
                                <span class="text-xl font-bold text-gray-800 dark:text-white">{{ $stat['value'] }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        {{-- Quick Actions --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            @foreach ([
                ['label' => 'إضافة مريضة', 'href' => '/admin/patients/new', 'icon' => 'users'],
                ['label' => 'حجز جديد', 'href' => '/admin/appointments/new', 'icon' => 'calendar'],
                ['label' => 'بدء دورة علاج', 'href' => '/admin/ivf/new', 'icon' => 'baby'],
                ['label' => 'إضافة تقرير', 'href' => '/admin/reports/new', 'icon' => 'activity'],
            ] as $action)
                <a href="{{ $action['href'] }}"
                   class="flex items-center gap-3 p-4 bg-white dark:bg-gray-800 rounded-xl shadow-md hover:shadow-lg transition-shadow group">
                    <div class="w-10 h-10 rounded-lg bg-medical-blue/10 flex items-center justify-center group-hover:bg-medical-blue transition-colors">
                        @svg('lucide-' . $action['icon'], 'w-5 h-5 text-medical-blue group-hover:text-white')
                    </div>
                    <span class="font-medium text-gray-700 dark:text-gray-300">{{ $action['label'] }}</span>
                </a>
            @endforeach
        </div>
    </div>
@endsection
