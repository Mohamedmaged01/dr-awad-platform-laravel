@extends('layouts.admin')

@php
    $menu = config('clinic.dashboard_menu');
    $roleLabels = config('clinic.role_labels');
    $roles = config('clinic.staff_roles');
@endphp

@section('content')
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-800 dark:text-white">{{ __('permMatrixTitle') }}</h1>
                <p class="text-gray-500">{{ __('permMatrixSubtitle') }}</p>
            </div>
        </div>

        <x-ui.card class="overflow-hidden">
            <x-ui.card-header class="bg-gray-50 dark:bg-gray-800/50">
                <div class="flex items-center gap-2">
                    @svg('lucide-shield', 'w-6 h-6 text-medical-blue')
                    <h2 class="text-lg font-bold">{{ __('permMatrix') }}</h2>
                </div>
            </x-ui.card-header>
            <x-ui.card-content class="p-0">
                <div class="overflow-x-auto">
                    <table class="w-full text-right border-collapse">
                        <thead>
                            <tr class="bg-gray-100 dark:bg-gray-700">
                                <th class="p-4 border-b border-gray-200 dark:border-gray-600 font-bold">{{ __('permFeatureCol') }}</th>
                                @foreach ($roles as $role)
                                    <th class="p-4 border-b border-gray-200 dark:border-gray-600 text-center font-bold">{{ __($roleLabels[$role]) }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($menu as $index => $item)
                                <tr class="{{ $index % 2 === 0 ? 'bg-white dark:bg-gray-800' : 'bg-gray-50/50 dark:bg-gray-800/50' }}">
                                    <td class="p-4 border-b border-gray-200 dark:border-gray-700">
                                        <div class="flex items-center gap-3">
                                            @svg('lucide-' . $item['icon'], 'w-[18px] h-[18px] text-gray-400')
                                            <span class="font-medium">{{ __($item['name']) }}</span>
                                        </div>
                                    </td>
                                    @foreach ($roles as $role)
                                        <td class="p-4 border-b border-gray-200 dark:border-gray-700 text-center">
                                            @if (in_array($role, $item['roles']))
                                                <div class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-green-100 dark:bg-green-900/30 text-green-600">
                                                    @svg('lucide-unlock', 'w-4 h-4')
                                                </div>
                                            @else
                                                <div class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-red-100 dark:bg-red-900/30 text-red-400">
                                                    @svg('lucide-lock', 'w-4 h-4')
                                                </div>
                                            @endif
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </x-ui.card-content>
        </x-ui.card>

        {{-- Role Descriptions --}}
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach ($roles as $role)
                @php $roleItems = collect($menu)->filter(fn ($item) => in_array($role, $item['roles']))->values(); @endphp
                <x-ui.card class="border-t-4 border-t-medical-blue">
                    <x-ui.card-header>
                        <h3 class="font-bold text-lg">{{ __($roleLabels[$role]) }}</h3>
                    </x-ui.card-header>
                    <x-ui.card-content>
                        <ul class="space-y-2">
                            @foreach ($roleItems->take(5) as $item)
                                <li class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                                    @svg('lucide-check', 'w-3.5 h-3.5 text-green-500')
                                    {{ __($item['name']) }}
                                </li>
                            @endforeach
                            @if ($roleItems->count() > 5)
                                <li class="text-xs text-medical-blue font-medium">{{ __('moreFeatures') }}</li>
                            @endif
                        </ul>
                    </x-ui.card-content>
                </x-ui.card>
            @endforeach
        </div>
    </div>
@endsection
