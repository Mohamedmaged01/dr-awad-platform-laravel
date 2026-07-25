@extends('layouts.admin')

@php
    $statusColors = ['scheduled' => 'blue', 'completed' => 'green', 'pending' => 'yellow', 'cancelled' => 'red'];
@endphp

@section('content')
<div x-data="{ addOpen: false, viewOpen: false, current: {} }">
    <x-admin.page-header :title="__('surgeries')">
        <x-slot:actions>
            <x-ui.button variant="primary" size="sm" x-on:click="addOpen = true">
                <x-slot:leftIcon>@svg('lucide-plus', 'w-[18px] h-[18px]')</x-slot:leftIcon>
                {{ __('add_new') }}
            </x-ui.button>
        </x-slot:actions>
    </x-admin.page-header>

    {{-- Stats --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        @foreach ($stats as $s)
            <x-admin.stat-tile :label="$s['label']" :value="$s['value']" :icon="$s['icon']" :color="$s['color']" />
        @endforeach
    </div>

    {{-- Filter --}}
    <x-ui.card class="mb-6">
        <x-ui.card-content class="p-4 flex flex-wrap gap-4">
            <div class="flex-1 min-w-[200px]">
                <x-ui.input name="search" :placeholder="__('search_placeholder')">
                    <x-slot:leftIcon>@svg('lucide-search', 'w-[18px] h-[18px]')</x-slot:leftIcon>
                </x-ui.input>
            </div>
            <x-ui.select name="status" :placeholder="__('status')" class="w-44" :options="[
                ['value' => 'scheduled', 'label' => __('scheduled')],
                ['value' => 'completed', 'label' => __('completed')],
                ['value' => 'pending', 'label' => __('pending')],
            ]" />
        </x-ui.card-content>
    </x-ui.card>

    {{-- Table --}}
    <x-ui.card>
        <x-ui.card-content class="p-0">
            <div class="overflow-x-auto">
                <table class="w-full text-start">
                    <thead class="bg-gray-50 dark:bg-gray-700/50">
                        <tr class="text-start text-sm font-semibold text-gray-600 dark:text-gray-300">
                            <th class="px-6 py-4 text-start">{{ __('patient_name') }}</th>
                            <th class="px-6 py-4 text-start">{{ __('surgery_name') }}</th>
                            <th class="px-6 py-4 text-start">{{ __('date') }}</th>
                            <th class="px-6 py-4 text-start">{{ __('doctor') }}</th>
                            <th class="px-6 py-4 text-start">{{ __('cost') }}</th>
                            <th class="px-6 py-4 text-start">{{ __('status') }}</th>
                            <th class="px-6 py-4 text-start">{{ __('actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach ($surgeries as $s)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-full bg-medical-blue/10 flex items-center justify-center text-medical-blue font-bold">{{ mb_substr($s['patient'], 0, 1) }}</div>
                                        <div>
                                            <p class="font-medium text-gray-800 dark:text-white">{{ $s['patient'] }}</p>
                                            <p class="text-xs text-gray-500">{{ $s['file'] }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="text-gray-800 dark:text-gray-200">{{ $s['operation'] }}</p>
                                    <p class="text-xs text-gray-500">{{ $s['type'] }}</p>
                                </td>
                                <td class="px-6 py-4 text-gray-600 dark:text-gray-400">
                                    <div class="flex items-center gap-2">@svg('lucide-calendar', 'w-3.5 h-3.5') {{ $s['date'] }}</div>
                                    <div class="flex items-center gap-2 text-xs text-gray-500">@svg('lucide-clock', 'w-3.5 h-3.5') {{ $s['time'] }}</div>
                                </td>
                                <td class="px-6 py-4 text-gray-600 dark:text-gray-400">{{ $s['doctor'] }}</td>
                                <td class="px-6 py-4 font-medium text-medical-blue">{{ number_format($s['cost']) }} {{ __('egp') }}</td>
                                <td class="px-6 py-4">
                                    <x-admin.badge :color="$statusColors[$s['status']] ?? 'gray'">{{ __($s['status']) }}</x-admin.badge>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-1">
                                        <button class="p-1.5 text-gray-400 hover:text-medical-blue" x-on:click="current = {{ Js::from($s) }}; viewOpen = true">@svg('lucide-eye', 'w-[18px] h-[18px]')</button>
                                        <button class="p-1.5 text-gray-400 hover:text-medical-blue">@svg('lucide-pencil', 'w-[18px] h-[18px]')</button>
                                        <button class="p-1.5 text-gray-400 hover:text-red-500">@svg('lucide-trash-2', 'w-[18px] h-[18px]')</button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </x-ui.card-content>
    </x-ui.card>

    {{-- Add modal --}}
    <x-admin.modal :title="__('add_new')" var="addOpen" max-width="max-w-2xl">
        <form class="grid md:grid-cols-2 gap-4" @submit.prevent="addOpen = false">
            <x-ui.input :label="__('patient_name')" name="patient" />
            <x-ui.input :label="__('surgery_name')" name="operation" />
            <x-ui.input :label="__('date')" name="date" type="date" />
            <x-ui.input :label="__('time')" name="time" type="time" />
            <x-ui.input :label="__('cost')" name="cost" type="number" />
            <x-ui.input :label="__('doctor')" name="doctor" value="د. محمد عوض" />
        </form>
        <x-slot:footer>
            <x-ui.button variant="outline" size="sm" x-on:click="addOpen = false">{{ __('cancel') }}</x-ui.button>
            <x-ui.button variant="primary" size="sm" x-on:click="addOpen = false">{{ __('save') }}</x-ui.button>
        </x-slot:footer>
    </x-admin.modal>

    {{-- View modal --}}
    <x-admin.modal :title="__('view_details')" var="viewOpen">
        <div class="space-y-3 text-sm">
            <p><span class="text-gray-500">{{ __('patient_name') }}:</span> <span class="font-medium" x-text="current.patient"></span></p>
            <p><span class="text-gray-500">{{ __('surgery_name') }}:</span> <span class="font-medium" x-text="current.operation"></span></p>
            <p><span class="text-gray-500">{{ __('date') }}:</span> <span class="font-medium" x-text="current.date + ' - ' + current.time"></span></p>
            <p><span class="text-gray-500">{{ __('cost') }}:</span> <span class="font-medium text-medical-blue" x-text="current.cost"></span></p>
        </div>
    </x-admin.modal>
</div>
@endsection
