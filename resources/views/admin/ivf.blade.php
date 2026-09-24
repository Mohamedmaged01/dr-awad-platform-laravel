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
    <div class="space-y-6" x-data="{ newOpen: false, viewOpen: false, editOpen: false, current: {}, search: '', stage: 'all',
            patients: {{ Js::from($patientData) }},
            matches(t, st) { return (this.search === '' || t.includes(this.search)) && (this.stage === 'all' || this.stage === st) },
            fillFromPatient() { const p = this.patients[this.current.patient_id] || {}; this.current.age = p.age ?? ''; this.current.phone = p.phone ?? ''; this.current.address = p.address ?? ''; } }">
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
                        <x-ui.input placeholder="بحث بالاسم..." x-model="search">
                            <x-slot:leftIcon>@svg('lucide-search', 'w-[18px] h-[18px]')</x-slot:leftIcon>
                        </x-ui.input>
                    </div>
                    <select x-model="stage"
                            class="px-4 py-2.5 border rounded-lg bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-medical-blue">
                        <option value="all">كل المراحل</option>
                        @foreach ($stageConfig as $key => $cfg)
                            <option value="{{ $key }}">{{ $cfg['label'] }}</option>
                        @endforeach
                    </select>
                </div>
            </x-ui.card-content>
        </x-ui.card>

        {{-- Cycles Grid --}}
        <div class="grid md:grid-cols-2 gap-6">
            @foreach ($cycles as $cycle)
                @php
                    $stage = $stageConfig[$cycle->current_stage] ?? $stageConfig['consultation'];
                    $cycleData = [
                        'id' => $cycle->id,
                        'patient' => $cycle->patient?->short_name ?? '—',
                        'patient_id' => $cycle->patient_id,
                        'file' => $cycle->patient?->file_number ?? '',
                        'cycle_number' => $cycle->cycle_number,
                        'cycle_type' => $cycle->cycle_type,
                        'protocol' => $cycle->protocol,
                        'dose' => $cycle->dose,
                        'current_stage' => $cycle->current_stage,
                        'stage_label' => $stage['label'],
                        'start_date' => optional($cycle->start_date)->format('Y-m-d'),
                        'stimulation_start_date' => optional($cycle->stimulation_start_date)->format('Y-m-d'),
                        'stimulation_end_date' => optional($cycle->stimulation_end_date)->format('Y-m-d'),
                        'egg_retrieval_date' => optional($cycle->egg_retrieval_date)->format('Y-m-d'),
                        'fertilization_date' => optional($cycle->fertilization_date)->format('Y-m-d'),
                        'embryo_transfer_date' => optional($cycle->embryo_transfer_date)->format('Y-m-d'),
                        'freezing_note' => $cycle->freezing_note,
                        'final_result' => $cycle->is_pregnant === null ? '' : ($cycle->is_pregnant ? 'positive' : 'negative'),
                        'age' => $cycle->patient?->age,
                        'phone' => $cycle->patient?->phone,
                        'address' => $cycle->patient?->address,
                    ];
                @endphp
                <div x-show="matches(@js(trim(($cycle->patient?->short_name ?? '') . ' ' . ($cycle->cycle_type ?? ''))), @js($cycle->current_stage))">
                <x-ui.card class="overflow-hidden">
                    <div class="h-2 {{ $stage['color'] }}" style="width: {{ $stage['progress'] }}%"></div>
                    <x-ui.card-content class="p-6">
                        <div class="flex items-start justify-between mb-4">
                            <div>
                                <h3 class="text-lg font-bold text-gray-800 dark:text-white">{{ $cycle->patient?->short_name ?? '—' }}</h3>
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
                                <button type="button" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg" title="{{ __('view_details') }}"
                                        x-on:click="current = {{ Js::from($cycleData) }}; viewOpen = true">@svg('lucide-eye', 'w-[18px] h-[18px] text-gray-500 hover:text-medical-blue')</button>
                                <button type="button" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg" title="{{ __('edit_details') }}"
                                        x-on:click="current = {{ Js::from($cycleData) }}; editOpen = true">@svg('lucide-pencil', 'w-[18px] h-[18px] text-gray-500 hover:text-medical-blue')</button>
                                <form method="POST" action="{{ route('admin.ivf.destroy', $cycle->id) }}" onsubmit="return confirm('{{ __('confirmDelete') }}')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg" title="{{ __('delete') }}">@svg('lucide-trash-2', 'w-[18px] h-[18px] text-gray-500 hover:text-red-500')</button>
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
                </div>
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
        <x-admin.modal title="{{ __('newCycleTitle') }}" var="newOpen" max-width="max-w-3xl">
            <form method="POST" action="{{ route('admin.ivf.store') }}" class="grid md:grid-cols-2 gap-4"
                  x-data="{
                      patients: {{ Js::from($patientData) }},
                      cid: '', age: '', phone: '', address: '',
                      fill() { const p = this.patients[this.cid] || {}; this.age = p.age ?? ''; this.phone = p.phone ?? ''; this.address = p.address ?? ''; },
                      init() { this.$watch('cid', () => this.fill()); }
                  }">
                @csrf

                {{-- Patient (pick existing; contact fields prefill and stay editable) --}}
                <div class="md:col-span-2">
                    <x-ui.select :label="__('patient_name')" name="patient_id" :options="$patientOptions" :placeholder="__('selectPatient')" x-model="cid" required />
                </div>
                <x-ui.input :label="__('age')" name="age" type="number" min="0" max="120" x-model="age" />
                <x-ui.input :label="__('phone')" name="phone" type="tel" x-model="phone" />
                <div class="md:col-span-2">
                    <x-ui.input :label="__('address')" name="address" x-model="address" />
                </div>

                {{-- Cycle basics --}}
                <x-ui.input :label="__('date')" name="start_date" type="date" value="{{ now()->toDateString() }}" required />
                <x-ui.select :label="__('cycleType')" name="cycle_type" :options="[
                    ['value' => 'ICSI', 'label' => 'ICSI'],
                    ['value' => 'IVF', 'label' => 'IVF'],
                    ['value' => 'IUI', 'label' => 'IUI'],
                ]" required />
                <x-ui.select :label="__('protocol')" name="protocol" :options="[
                    ['value' => 'agonist', 'label' => 'Agonist'],
                    ['value' => 'antagonist', 'label' => 'Antagonist'],
                ]" required />
                <x-ui.select :label="__('dose')" name="dose" :options="[
                    ['value' => 'normal', 'label' => __('doseNormal')],
                    ['value' => 'full', 'label' => __('doseFull')],
                ]" />
                <div class="md:col-span-2">
                    <x-ui.select :label="__('cycleStage')" name="current_stage" :options="collect($stageConfig)->map(fn ($c, $k) => ['value' => $k, 'label' => $c['label']])->values()->all()" required />
                </div>

                {{-- Stimulation --}}
                <x-ui.input :label="__('stimulationStart')" name="stimulation_start_date" type="date" />
                <x-ui.input :label="__('stimulationEnd')" name="stimulation_end_date" type="date" />

                {{-- Procedure dates + outcome --}}
                <x-ui.input :label="__('eggRetrievalDate')" name="egg_retrieval_date" type="date" />
                <x-ui.input :label="__('fertilizationDate')" name="fertilization_date" type="date" />
                <x-ui.input :label="__('embryoTransferDate')" name="embryo_transfer_date" type="date" />
                <x-ui.input :label="__('freezing')" name="freezing_note" :placeholder="__('freezingPlaceholder')" />
                <div class="md:col-span-2">
                    <x-ui.select :label="__('finalResult')" name="final_result" :options="[
                        ['value' => 'positive', 'label' => __('resultPositive')],
                        ['value' => 'negative', 'label' => __('resultNegative')],
                    ]" :placeholder="__('notDetermined')" />
                </div>

                <div class="md:col-span-2 flex justify-end gap-3 pt-4 border-t border-gray-100 dark:border-gray-700">
                    <x-ui.button type="button" variant="outline" size="sm" x-on:click="newOpen = false">{{ __('cancel') }}</x-ui.button>
                    <x-ui.button type="submit" variant="primary" size="sm">{{ __('startCycle') }}</x-ui.button>
                </div>
            </form>
        </x-admin.modal>

        {{-- Edit cycle modal --}}
        <x-admin.modal :title="__('edit_details')" var="editOpen" max-width="max-w-3xl">
            <form :action="'{{ url('/admin/ivf') }}/' + current.id" method="POST" class="grid md:grid-cols-2 gap-4">
                @csrf @method('PUT')

                <div class="md:col-span-2">
                    <x-ui.select :label="__('patient_name')" name="patient_id" :options="$patientOptions" x-model="current.patient_id" x-on:change="fillFromPatient()" required />
                </div>
                <x-ui.input :label="__('age')" name="age" type="number" min="0" max="120" x-model="current.age" />
                <x-ui.input :label="__('phone')" name="phone" type="tel" x-model="current.phone" />
                <div class="md:col-span-2">
                    <x-ui.input :label="__('address')" name="address" x-model="current.address" />
                </div>

                <x-ui.input :label="__('date')" name="start_date" type="date" x-model="current.start_date" required />
                <x-ui.select :label="__('cycleType')" name="cycle_type" :options="[
                    ['value' => 'ICSI', 'label' => 'ICSI'],
                    ['value' => 'IVF', 'label' => 'IVF'],
                    ['value' => 'IUI', 'label' => 'IUI'],
                ]" x-model="current.cycle_type" required />
                <x-ui.select :label="__('protocol')" name="protocol" :options="[
                    ['value' => 'agonist', 'label' => 'Agonist'],
                    ['value' => 'antagonist', 'label' => 'Antagonist'],
                ]" x-model="current.protocol" required />
                <x-ui.select :label="__('dose')" name="dose" :options="[
                    ['value' => 'normal', 'label' => __('doseNormal')],
                    ['value' => 'full', 'label' => __('doseFull')],
                ]" x-model="current.dose" />
                <div class="md:col-span-2">
                    <x-ui.select :label="__('cycleStage')" name="current_stage" :options="collect($stageConfig)->map(fn ($c, $k) => ['value' => $k, 'label' => $c['label']])->values()->all()" x-model="current.current_stage" required />
                </div>

                <x-ui.input :label="__('stimulationStart')" name="stimulation_start_date" type="date" x-model="current.stimulation_start_date" />
                <x-ui.input :label="__('stimulationEnd')" name="stimulation_end_date" type="date" x-model="current.stimulation_end_date" />

                <x-ui.input :label="__('eggRetrievalDate')" name="egg_retrieval_date" type="date" x-model="current.egg_retrieval_date" />
                <x-ui.input :label="__('fertilizationDate')" name="fertilization_date" type="date" x-model="current.fertilization_date" />
                <x-ui.input :label="__('embryoTransferDate')" name="embryo_transfer_date" type="date" x-model="current.embryo_transfer_date" />
                <x-ui.input :label="__('freezing')" name="freezing_note" x-model="current.freezing_note" :placeholder="__('freezingPlaceholder')" />
                <div class="md:col-span-2">
                    <x-ui.select :label="__('finalResult')" name="final_result" :options="[
                        ['value' => 'positive', 'label' => __('resultPositive')],
                        ['value' => 'negative', 'label' => __('resultNegative')],
                    ]" x-model="current.final_result" :placeholder="__('notDetermined')" />
                </div>

                <div class="md:col-span-2 flex justify-end gap-3 pt-4 border-t border-gray-100 dark:border-gray-700">
                    <x-ui.button type="button" variant="outline" size="sm" x-on:click="editOpen = false">{{ __('cancel') }}</x-ui.button>
                    <x-ui.button type="submit" variant="primary" size="sm">{{ __('save_changes') }}</x-ui.button>
                </div>
            </form>
        </x-admin.modal>

        {{-- View cycle modal --}}
        <x-admin.modal :title="__('view_details')" var="viewOpen" max-width="max-w-2xl">
            <div class="grid md:grid-cols-2 gap-x-6 gap-y-3 text-sm">
                @php
                    $viewRows = [
                        ['patient_name', 'patient'],
                        ['fileNumber', 'file'],
                        ['cycleType', 'cycle_type'],
                        ['protocol', 'protocol'],
                        ['dose', 'dose'],
                        ['cycleStage', 'stage_label'],
                        ['age', 'age'],
                        ['phone', 'phone'],
                        ['address', 'address'],
                        ['date', 'start_date'],
                        ['stimulationStart', 'stimulation_start_date'],
                        ['stimulationEnd', 'stimulation_end_date'],
                        ['eggRetrievalDate', 'egg_retrieval_date'],
                        ['fertilizationDate', 'fertilization_date'],
                        ['embryoTransferDate', 'embryo_transfer_date'],
                        ['freezing', 'freezing_note'],
                    ];
                @endphp
                @foreach ($viewRows as [$label, $key])
                    <div class="flex flex-col">
                        <span class="text-gray-400 text-xs">{{ __($label) }}</span>
                        <span class="font-medium text-gray-800 dark:text-white" x-text="current.{{ $key }} || '—'"></span>
                    </div>
                @endforeach
                <div class="flex flex-col">
                    <span class="text-gray-400 text-xs">{{ __('finalResult') }}</span>
                    <span class="font-medium text-gray-800 dark:text-white"
                          x-text="current.final_result === 'positive' ? '{{ __('resultPositive') }}' : (current.final_result === 'negative' ? '{{ __('resultNegative') }}' : '{{ __('notDetermined') }}')"></span>
                </div>
            </div>
            <div class="flex justify-end gap-3 pt-4 mt-4 border-t border-gray-100 dark:border-gray-700">
                <x-ui.button type="button" variant="outline" size="sm" x-on:click="viewOpen = false">{{ __('close') }}</x-ui.button>
                <x-ui.button type="button" variant="primary" size="sm" x-on:click="viewOpen = false; editOpen = true">{{ __('edit_details') }}</x-ui.button>
            </div>
        </x-admin.modal>
    </div>
@endsection
