@extends('layouts.admin')

@php
    $stageConfig = [
        'consultation' => ['label' => 'استشارة', 'color' => 'bg-gray-500', 'progress' => 10],
        'stimulation' => ['label' => 'تنشيط', 'color' => 'bg-blue-500', 'progress' => 30],
        'egg_retrieval' => ['label' => 'سحب البويضات', 'color' => 'bg-purple-500', 'progress' => 50],
        'fertilization' => ['label' => 'التخصيب', 'color' => 'bg-pink-500', 'progress' => 60],
        'embryo_transfer' => ['label' => 'زرع الأجنة', 'color' => 'bg-green-500', 'progress' => 80],
        'pregnancy_test' => ['label' => 'تحليل الحمل', 'color' => 'bg-yellow-500', 'progress' => 95],
        'completed' => ['label' => 'مكتملة', 'color' => 'bg-emerald-500', 'progress' => 100],
    ];
    $taskTypeLabels = ['follow-up' => 'متابعة', 'retrieval' => 'سحب بويضات', 'transfer' => 'زرع أجنة', 'test' => 'تحليل حمل'];
@endphp

@section('content')
    <div class="space-y-6" x-data="{ newOpen: false }">
        {{-- Header --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-800 dark:text-white">مركز الحقن المجهري</h1>
                <p class="text-gray-500">{{ $cycles->count() }} دورة علاج نشطة</p>
            </div>
            <x-ui.button variant="primary" x-on:click="newOpen = true">
                <x-slot:leftIcon>@svg('lucide-plus', 'w-[18px] h-[18px]')</x-slot:leftIcon>
                بدء دورة جديدة
            </x-ui.button>
        </div>

        {{-- Stats --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            @foreach ($stats as $stat)
                <x-ui.card>
                    <x-ui.card-content class="p-4 flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl {{ $stat['color'] }} flex items-center justify-center">
                            @svg('lucide-' . $stat['icon'], 'w-6 h-6 text-white')
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-gray-800 dark:text-white">{{ $stat['value'] }}</p>
                            <p class="text-sm text-gray-500">{{ $stat['label'] }}</p>
                        </div>
                    </x-ui.card-content>
                </x-ui.card>
            @endforeach
        </div>

        {{-- Filters --}}
        <x-ui.card>
            <x-ui.card-content class="p-4">
                <div class="flex flex-col md:flex-row gap-4">
                    <div class="flex-1">
                        <x-ui.input placeholder="بحث بالاسم...">
                            <x-slot:leftIcon>@svg('lucide-search', 'w-[18px] h-[18px]')</x-slot:leftIcon>
                        </x-ui.input>
                    </div>
                    <x-ui.button variant="outline">
                        <x-slot:leftIcon>@svg('lucide-filter', 'w-[18px] h-[18px]')</x-slot:leftIcon>
                        تصفية حسب المرحلة
                    </x-ui.button>
                </div>
            </x-ui.card-content>
        </x-ui.card>

        {{-- Cycles Grid --}}
        <div class="grid md:grid-cols-2 gap-6">
            @foreach ($cycles as $cycle)
                @php $stage = $stageConfig[$cycle->current_stage] ?? $stageConfig['consultation']; @endphp
                <x-ui.card class="overflow-hidden">
                    <div class="h-2 {{ $stage['color'] }}" style="width: {{ $stage['progress'] }}%"></div>
                    <x-ui.card-content class="p-6">
                        <div class="flex items-start justify-between mb-4">
                            <div>
                                <h3 class="text-lg font-bold text-gray-800 dark:text-white">{{ $cycle->patient->short_name }}</h3>
                                <p class="text-sm text-gray-500">{{ $cycle->cycle_type }} - الدورة #{{ $cycle->cycle_number }}</p>
                            </div>
                            <span class="px-3 py-1 rounded-full text-xs font-medium {{ $stage['color'] }} text-white">{{ $stage['label'] }}</span>
                        </div>

                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div class="p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                <p class="text-xs text-gray-500 mb-1">البروتوكول</p>
                                <p class="font-medium text-gray-800 dark:text-white">{{ $cycle->protocol }}</p>
                            </div>
                            <div class="p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                <p class="text-xs text-gray-500 mb-1">يوم الدورة</p>
                                <p class="font-medium text-gray-800 dark:text-white">اليوم {{ $cycle->latestFollowup?->day_of_cycle }}</p>
                            </div>
                        </div>

                        <div class="flex items-center justify-between text-sm">
                            <div class="flex items-center gap-2 text-gray-500">
                                @svg('lucide-calendar', 'w-4 h-4')
                                <span>الموعد القادم: {{ \Carbon\Carbon::parse($cycle->latestFollowup?->next_appointment)->locale('ar_EG')->isoFormat('D/M/YYYY') }}</span>
                            </div>
                            <div class="flex gap-2">
                                <form method="POST" action="{{ route('admin.ivf.destroy', $cycle->id) }}" onsubmit="return confirm('{{ __('confirmDelete') }}')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg">@svg('lucide-trash-2', 'w-[18px] h-[18px] text-gray-500 hover:text-red-500')</button>
                                </form>
                            </div>
                        </div>

                        {{-- Progress Bar --}}
                        <div class="mt-4">
                            <div class="flex justify-between text-xs text-gray-500 mb-1">
                                <span>تقدم الدورة</span>
                                <span>{{ $stage['progress'] }}%</span>
                            </div>
                            <div class="h-2 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                                <div class="h-full {{ $stage['color'] }} rounded-full transition-all duration-500" style="width: {{ $stage['progress'] }}%"></div>
                            </div>
                        </div>
                    </x-ui.card-content>
                </x-ui.card>
            @endforeach
        </div>

        {{-- Today's Tasks --}}
        <x-ui.card>
            <x-ui.card-header>
                <h2 class="text-lg font-bold text-gray-800 dark:text-white">مهام اليوم</h2>
            </x-ui.card-header>
            <x-ui.card-content>
                <div class="space-y-3">
                    @foreach ($tasks as $task)
                        <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-xl">
                            <div class="flex items-center gap-4">
                                <div class="text-lg font-bold text-medical-blue">{{ $task['time'] }}</div>
                                <div class="w-px h-8 bg-gray-200 dark:bg-gray-600"></div>
                                <div>
                                    <p class="font-medium text-gray-800 dark:text-white">{{ $task['task'] }}</p>
                                    <p class="text-xs text-gray-500">{{ $taskTypeLabels[$task['type']] ?? '' }}</p>
                                </div>
                            </div>
                            <x-ui.button variant="outline" size="sm">
                                عرض
                                <x-slot:rightIcon>@svg('lucide-chevron-left', 'w-4 h-4')</x-slot:rightIcon>
                            </x-ui.button>
                        </div>
                    @endforeach
                </div>
            </x-ui.card-content>
        </x-ui.card>

        {{-- New cycle modal --}}
        <x-admin.modal title="{{ __('newCycleTitle') }}" var="newOpen" max-width="max-w-2xl">
            <form method="POST" action="{{ route('admin.ivf.store') }}" class="grid md:grid-cols-2 gap-4">
                @csrf
                <div class="md:col-span-2">
                    <x-ui.select :label="__('patient_name')" name="patient_id" :options="$patientOptions" :placeholder="__('selectPatient')" required />
                </div>
                <x-ui.select :label="__('cycleType')" name="cycle_type" :options="[
                    ['value' => 'ICSI', 'label' => 'ICSI'],
                    ['value' => 'IVF', 'label' => 'IVF'],
                    ['value' => 'IUI', 'label' => 'IUI'],
                ]" required />
                <x-ui.input :label="__('protocol')" name="protocol" required />
                <x-ui.select :label="__('cycleStage')" name="current_stage" :options="collect($stageConfig)->map(fn ($c, $k) => ['value' => $k, 'label' => $c['label']])->values()->all()" required />
                <x-ui.input :label="__('dayOfCycle')" name="day_of_cycle" type="number" min="1" value="1" />
                <x-ui.input :label="__('date')" name="start_date" type="date" required />
                <x-ui.input :label="__('nextAppointment')" name="next_appointment" type="date" />
                <div class="md:col-span-2 flex justify-end gap-3 pt-4 border-t border-gray-100 dark:border-gray-700">
                    <x-ui.button type="button" variant="outline" size="sm" x-on:click="newOpen = false">{{ __('cancel') }}</x-ui.button>
                    <x-ui.button type="submit" variant="primary" size="sm">{{ __('startCycle') }}</x-ui.button>
                </div>
            </form>
        </x-admin.modal>
    </div>
@endsection
