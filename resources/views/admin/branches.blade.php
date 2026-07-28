@extends('layouts.admin')

@section('content')
<div x-data="{ addOpen: false, editOpen: false, current: {} }">
    <x-admin.page-header :title="__('branches')">
        <x-slot:actions>
            <x-ui.button variant="primary" size="sm" x-on:click="addOpen = true">
                <x-slot:leftIcon>@svg('lucide-plus', 'w-[18px] h-[18px]')</x-slot:leftIcon>
                {{ __('add_new') }}
            </x-ui.button>
        </x-slot:actions>
    </x-admin.page-header>

    <div class="grid sm:grid-cols-2 gap-6">
        @foreach ($branches as $b)
            <x-ui.card class="overflow-hidden">
                <div class="h-24 bg-gradient-to-r from-medical-blue to-medical-blue-dark relative">
                    <div class="absolute -bottom-6 start-6 w-14 h-14 rounded-2xl bg-white dark:bg-gray-800 shadow-lg flex items-center justify-center">
                        @svg('lucide-building', 'w-7 h-7 text-medical-blue')
                    </div>
                </div>
                <x-ui.card-content class="p-6 pt-10">
                    <h3 class="font-bold text-lg text-gray-800 dark:text-white mb-3">{{ $b['name'] }}</h3>
                    <div class="space-y-2 text-sm text-gray-600 dark:text-gray-400">
                        <p class="flex items-start gap-2">@svg('lucide-map-pin', 'w-4 h-4 text-medical-blue flex-shrink-0 mt-0.5') {{ $b['address'] }}</p>
                        <p class="flex items-center gap-2">@svg('lucide-phone', 'w-4 h-4 text-medical-blue flex-shrink-0') {{ $b['phone'] }}</p>
                        <p class="flex items-center gap-2">@svg('lucide-clock', 'w-4 h-4 text-medical-blue flex-shrink-0') {{ $b['hours'] }}</p>
                    </div>
                    <div class="flex items-center gap-1 mt-4 pt-4 border-t border-gray-100 dark:border-gray-700">
                        <button type="button" class="p-1.5 text-gray-400 hover:text-medical-blue"
                                x-on:click="current = {{ Js::from($b) }}; editOpen = true">
                            @svg('lucide-pencil', 'w-[18px] h-[18px]')
                        </button>
                        <form method="POST" action="{{ route('admin.branches.destroy', $b['id']) }}"
                              onsubmit="return confirm('{{ __('confirmDelete') }}')">
                            @csrf @method('DELETE')
                            <button type="submit" class="p-1.5 text-gray-400 hover:text-red-500">@svg('lucide-trash-2', 'w-[18px] h-[18px]')</button>
                        </form>
                    </div>
                </x-ui.card-content>
            </x-ui.card>
        @endforeach
    </div>

    {{-- Add --}}
    <x-admin.modal :title="__('add_new')" var="addOpen">
        <form method="POST" action="{{ route('admin.branches.store') }}" class="space-y-4">
            @csrf
            <x-ui.input :label="__('branch')" name="name" required />
            <x-ui.textarea :label="__('location')" name="address" rows="2" required />
            <x-ui.input :label="__('phone')" name="phone" required />
            <x-ui.input :label="__('workingHoursLabel')" name="hours" />
            <div class="flex justify-end gap-3 pt-4 border-t border-gray-100 dark:border-gray-700">
                <x-ui.button type="button" variant="outline" size="sm" x-on:click="addOpen = false">{{ __('cancel') }}</x-ui.button>
                <x-ui.button type="submit" variant="primary" size="sm">{{ __('save') }}</x-ui.button>
            </div>
        </form>
    </x-admin.modal>

    {{-- Edit --}}
    <x-admin.modal :title="__('edit_details')" var="editOpen">
        <form :action="'{{ url('/admin/branches') }}/' + current.id" method="POST" class="space-y-4">
            @csrf @method('PUT')
            <x-ui.input :label="__('branch')" name="name" x-model="current.name" required />
            <x-ui.textarea :label="__('location')" name="address" rows="2" x-model="current.address" required />
            <x-ui.input :label="__('phone')" name="phone" x-model="current.phone" required />
            <x-ui.input :label="__('workingHoursLabel')" name="hours" x-model="current.hours" />
            <div class="flex justify-end gap-3 pt-4 border-t border-gray-100 dark:border-gray-700">
                <x-ui.button type="button" variant="outline" size="sm" x-on:click="editOpen = false">{{ __('cancel') }}</x-ui.button>
                <x-ui.button type="submit" variant="primary" size="sm">{{ __('save_changes') }}</x-ui.button>
            </div>
        </form>
    </x-admin.modal>
</div>
@endsection
