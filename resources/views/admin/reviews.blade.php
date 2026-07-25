@extends('layouts.admin')

@php $statusColors = ['approved' => 'green', 'pending' => 'yellow', 'rejected' => 'red']; @endphp

@section('content')
<div>
    <x-admin.page-header :title="__('reviews_admin')">
        <x-slot:actions>
            <x-ui.button variant="outline" size="sm">
                <x-slot:leftIcon>@svg('lucide-download', 'w-[18px] h-[18px]')</x-slot:leftIcon>
                {{ __('export') }}
            </x-ui.button>
        </x-slot:actions>
    </x-admin.page-header>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        <x-ui.card class="bg-green-50 dark:bg-green-900/20"><x-ui.card-content class="p-6 text-center"><p class="text-3xl font-bold text-green-600">4.9</p><p class="text-sm text-gray-600 dark:text-gray-400">{{ __('patientRating') }}</p></x-ui.card-content></x-ui.card>
        <x-ui.card class="bg-blue-50 dark:bg-blue-900/20"><x-ui.card-content class="p-6 text-center"><p class="text-3xl font-bold text-blue-600">150+</p><p class="text-sm text-gray-600 dark:text-gray-400">{{ __('successStories') }}</p></x-ui.card-content></x-ui.card>
        <x-ui.card class="bg-yellow-50 dark:bg-yellow-900/20"><x-ui.card-content class="p-6 text-center"><p class="text-3xl font-bold text-yellow-600">12</p><p class="text-sm text-gray-600 dark:text-gray-400">{{ __('pending') }}</p></x-ui.card-content></x-ui.card>
    </div>

    <x-ui.card>
        <x-ui.card-content class="p-0">
            <div class="overflow-x-auto">
                <table class="w-full text-start">
                    <thead class="bg-gray-50 dark:bg-gray-700/50">
                        <tr class="text-sm font-semibold text-gray-600 dark:text-gray-300">
                            <th class="px-6 py-4 text-start">{{ __('patient_name') }}</th>
                            <th class="px-6 py-4 text-start">{{ __('patientRating') }}</th>
                            <th class="px-6 py-4 text-start">{{ __('details') }}</th>
                            <th class="px-6 py-4 text-start">{{ __('serviceLabel') }}</th>
                            <th class="px-6 py-4 text-start">{{ __('status') }}</th>
                            <th class="px-6 py-4 text-start">{{ __('actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach ($reviews as $r)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                                <td class="px-6 py-4 font-medium text-gray-800 dark:text-gray-200">{{ $r['patient'] }}</td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-0.5">
                                        @for ($i = 0; $i < 5; $i++)
                                            @svg('lucide-star', 'w-4 h-4 ' . ($i < $r['rating'] ? 'text-yellow-400 fill-yellow-400' : 'text-gray-300'))
                                        @endfor
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-gray-600 dark:text-gray-400 max-w-xs truncate">{{ $r['comment'] }}</td>
                                <td class="px-6 py-4 text-gray-600 dark:text-gray-400">{{ $r['service'] }}</td>
                                <td class="px-6 py-4"><x-admin.badge :color="$statusColors[$r['status']] ?? 'gray'">{{ $r['status'] === 'approved' ? __('completed') : __('pending') }}</x-admin.badge></td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-1">
                                        @if ($r['status'] === 'pending')
                                            <button class="p-1.5 text-gray-400 hover:text-green-500">@svg('lucide-check-circle', 'w-[18px] h-[18px]')</button>
                                        @endif
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
</div>
@endsection
