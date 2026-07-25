@extends('layouts.admin')

@php $statusColors = ['active' => 'green', 'vacation' => 'yellow', 'inactive' => 'red']; @endphp

@section('content')
<div x-data="{ addOpen: false }">
    <x-admin.page-header :title="__('staff')">
        <x-slot:actions>
            <x-ui.button variant="outline" size="sm">
                <x-slot:leftIcon>@svg('lucide-download', 'w-[18px] h-[18px]')</x-slot:leftIcon>
                {{ __('export') }}
            </x-ui.button>
            <x-ui.button variant="primary" size="sm" x-on:click="addOpen = true">
                <x-slot:leftIcon>@svg('lucide-plus', 'w-[18px] h-[18px]')</x-slot:leftIcon>
                {{ __('add_new') }}
            </x-ui.button>
        </x-slot:actions>
    </x-admin.page-header>

    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach ($staff as $m)
            <x-ui.card class="border-t-4 border-t-medical-blue">
                <x-ui.card-content class="p-6">
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex items-center gap-3">
                            <div class="w-14 h-14 rounded-full bg-medical-blue/10 flex items-center justify-center text-medical-blue text-xl font-bold">{{ mb_substr($m['name'], 0, 1) }}</div>
                            <div>
                                <p class="font-bold text-gray-800 dark:text-white">{{ $m['name'] }}</p>
                                <p class="text-sm text-gray-500">{{ $m['role'] }}</p>
                            </div>
                        </div>
                        <x-admin.badge :color="$statusColors[$m['status']] ?? 'gray'">{{ __($m['status']) }}</x-admin.badge>
                    </div>
                    <div class="space-y-2 text-sm text-gray-600 dark:text-gray-400 mb-4">
                        <p class="flex items-center gap-2">@svg('lucide-mail', 'w-4 h-4 text-gray-400') {{ $m['email'] }}</p>
                        <p class="flex items-center gap-2">@svg('lucide-phone', 'w-4 h-4 text-gray-400') {{ $m['phone'] }}</p>
                    </div>
                    <div class="flex items-center gap-1 pt-3 border-t border-gray-100 dark:border-gray-700">
                        <button class="p-1.5 text-gray-400 hover:text-medical-blue">@svg('lucide-eye', 'w-[18px] h-[18px]')</button>
                        <button class="p-1.5 text-gray-400 hover:text-medical-blue">@svg('lucide-pencil', 'w-[18px] h-[18px]')</button>
                        <button class="p-1.5 text-gray-400 hover:text-red-500">@svg('lucide-trash-2', 'w-[18px] h-[18px]')</button>
                    </div>
                </x-ui.card-content>
            </x-ui.card>
        @endforeach
    </div>

    <x-admin.modal :title="__('add_new')" var="addOpen">
        <form class="grid md:grid-cols-2 gap-4" @submit.prevent="addOpen = false">
            <x-ui.input :label="__('fullName')" name="name" />
            <x-ui.input :label="__('job_role')" name="role" />
            <x-ui.input :label="__('email')" name="email" type="email" />
            <x-ui.input :label="__('phone')" name="phone" />
        </form>
        <x-slot:footer>
            <x-ui.button variant="outline" size="sm" x-on:click="addOpen = false">{{ __('cancel') }}</x-ui.button>
            <x-ui.button variant="primary" size="sm" x-on:click="addOpen = false">{{ __('save') }}</x-ui.button>
        </x-slot:footer>
    </x-admin.modal>
</div>
@endsection
