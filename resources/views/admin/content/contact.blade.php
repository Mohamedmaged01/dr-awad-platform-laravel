@extends('layouts.admin')

@section('title', __('content_admin'))

@php
    $blank = ['id' => '', 'name_ar' => '', 'name_en' => '', 'address_ar' => '', 'address_en' => '', 'phone' => '', 'whatsapp' => '', 'email' => '', 'google_maps_url' => '', 'hours' => ''];
@endphp

@section('content')
@include('admin.content._nav', ['active' => 'contact'])

<div x-data="{ open: false, mode: 'add', current: @js($blank) }">
    <x-admin.page-header :title="__('contact')">
        <x-slot:actions>
            <x-ui.button variant="primary" size="sm" x-on:click="mode = 'add'; current = @js($blank); open = true">
                <x-slot:leftIcon>@svg('lucide-plus', 'w-[18px] h-[18px]')</x-slot:leftIcon>
                {{ __('addBranch') }}
            </x-ui.button>
        </x-slot:actions>
    </x-admin.page-header>

    {{-- Contact intro --}}
    <x-ui.card class="mb-6">
        <x-ui.card-header><h2 class="font-bold text-gray-800 dark:text-white">{{ __('contactIntro') }}</h2></x-ui.card-header>
        <x-ui.card-content>
            <form method="POST" action="{{ route('admin.content.contact.intro') }}" class="space-y-4">
                @csrf @method('PUT')
                <x-ui.textarea :label="__('contactIntro') . ' (AR)'" name="intro_ar" rows="2">{{ $introAr }}</x-ui.textarea>
                <x-ui.textarea :label="__('contactIntro') . ' (EN)'" name="intro_en" rows="2">{{ $introEn }}</x-ui.textarea>
                <div class="flex justify-end"><x-ui.button type="submit" variant="primary" size="sm">{{ __('save_changes') }}</x-ui.button></div>
            </form>
        </x-ui.card-content>
    </x-ui.card>

    {{-- Branches --}}
    <x-ui.card>
        <x-ui.card-content class="p-0">
            <div class="overflow-x-auto">
                <table class="w-full text-start">
                    <thead class="bg-gray-50 dark:bg-gray-700/50">
                        <tr class="text-sm font-semibold text-gray-600 dark:text-gray-300">
                            <th class="px-6 py-4 text-start">{{ __('branch') }}</th>
                            <th class="px-6 py-4 text-start">{{ __('phone') }}</th>
                            <th class="px-6 py-4 text-start">{{ __('location') }}</th>
                            <th class="px-6 py-4 text-start">{{ __('actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse ($branches as $b)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                                <td class="px-6 py-4 font-medium text-gray-800 dark:text-gray-200">{{ $b->name_ar }}</td>
                                <td class="px-6 py-4 text-gray-600 dark:text-gray-400">{{ $b->phone }}</td>
                                <td class="px-6 py-4 text-gray-600 dark:text-gray-400 max-w-xs truncate">{{ $b->address_ar }}</td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-1">
                                        <button class="p-1.5 text-gray-400 hover:text-medical-blue"
                                                x-on:click="mode = 'edit'; current = {{ Js::from([
                                                    'id' => $b->id, 'name_ar' => $b->name_ar, 'name_en' => $b->name_en,
                                                    'address_ar' => $b->address_ar, 'address_en' => $b->address_en,
                                                    'phone' => $b->phone, 'whatsapp' => $b->whatsapp, 'email' => $b->email,
                                                    'google_maps_url' => $b->google_maps_url, 'hours' => $b->working_hours['display'] ?? '',
                                                ]) }}; open = true">@svg('lucide-pencil', 'w-[18px] h-[18px]')</button>
                                        <form method="POST" action="{{ route('admin.content.contact.branches.destroy', $b->id) }}" onsubmit="return confirm('{{ __('confirmDelete') }}')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="p-1.5 text-gray-400 hover:text-red-500">@svg('lucide-trash-2', 'w-[18px] h-[18px]')</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="px-6 py-10 text-center text-gray-400">{{ __('noRecords') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-ui.card-content>
    </x-ui.card>

    <x-admin.modal :title="__('branchDetails')" var="open" max-width="max-w-2xl">
        <form method="POST"
              :action="mode === 'edit' ? '{{ url('/admin/content/contact/branches') }}/' + current.id : '{{ route('admin.content.contact.branches.store') }}'"
              class="space-y-4">
            @csrf
            <input type="hidden" name="_method" :value="mode === 'edit' ? 'PUT' : 'POST'">

            <div class="grid md:grid-cols-2 gap-4">
                <x-ui.input :label="__('branch') . ' (AR)'" name="name_ar" x-model="current.name_ar" required />
                <x-ui.input :label="__('branch') . ' (EN)'" name="name_en" x-model="current.name_en" />
            </div>
            <x-ui.input :label="__('location') . ' (AR)'" name="address_ar" x-model="current.address_ar" required />
            <x-ui.input :label="__('location') . ' (EN)'" name="address_en" x-model="current.address_en" />
            <div class="grid md:grid-cols-2 gap-4">
                <x-ui.input :label="__('phone')" name="phone" x-model="current.phone" required />
                <x-ui.input label="واتساب" name="whatsapp" x-model="current.whatsapp" />
                <x-ui.input :label="__('email')" name="email" type="email" x-model="current.email" />
                <x-ui.input :label="__('workingHoursLabel')" name="hours" x-model="current.hours" placeholder="يومياً 9 ص - 10 م" />
            </div>
            <x-ui.input :label="__('mapUrl')" name="google_maps_url" x-model="current.google_maps_url" placeholder="https://maps.google.com/..." />

            <div class="flex justify-end gap-3 pt-4 border-t border-gray-100 dark:border-gray-700">
                <x-ui.button type="button" variant="outline" size="sm" x-on:click="open = false">{{ __('cancel') }}</x-ui.button>
                <x-ui.button type="submit" variant="primary" size="sm">{{ __('save_changes') }}</x-ui.button>
            </div>
        </form>
    </x-admin.modal>
</div>
@endsection
