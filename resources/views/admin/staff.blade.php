@extends('layouts.admin')

@php $statusColors = ['active' => 'green', 'vacation' => 'yellow', 'inactive' => 'red']; @endphp

@section('content')
<div x-data="{ addOpen: false, editOpen: false, current: {} }">
    <x-admin.page-header :title="__('staff')">
        <x-slot:actions>
            <x-ui.button variant="primary" size="sm" x-on:click="addOpen = true">
                <x-slot:leftIcon>@svg('lucide-plus', 'w-[18px] h-[18px]')</x-slot:leftIcon>
                {{ __('add_new') }}
            </x-ui.button>
        </x-slot:actions>
    </x-admin.page-header>

    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse ($staff as $m)
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
                        <button class="p-1.5 text-gray-400 hover:text-medical-blue" x-on:click="current = {{ Js::from($m) }}; editOpen = true">@svg('lucide-pencil', 'w-[18px] h-[18px]')</button>
                        <form method="POST" action="{{ route('admin.staff.destroy', $m['id']) }}" onsubmit="return confirm('{{ __('confirmDelete') }}')">
                            @csrf @method('DELETE')
                            <button type="submit" class="p-1.5 text-gray-400 hover:text-red-500">@svg('lucide-trash-2', 'w-[18px] h-[18px]')</button>
                        </form>
                    </div>
                </x-ui.card-content>
            </x-ui.card>
        @empty
            <p class="col-span-full text-center text-gray-400 py-10">{{ __('noRecords') }}</p>
        @endforelse
    </div>

    {{-- Add --}}
    <x-admin.modal :title="__('add_new')" var="addOpen">
        <form method="POST" action="{{ route('admin.staff.store') }}" class="grid md:grid-cols-2 gap-4">
            @csrf
            <x-ui.input :label="__('firstName')" name="first_name_ar" required />
            <x-ui.input :label="__('lastName')" name="last_name_ar" required />
            <x-ui.input :label="__('email')" name="email" type="email" required />
            <x-ui.input :label="__('password')" name="password" type="password" required />
            <x-ui.select :label="__('job_role')" name="role" :options="$roleOptions" required />
            <x-ui.input :label="__('jobTitle')" name="title" />
            <x-ui.input :label="__('phone')" name="phone" />
            <x-ui.input :label="__('specialization')" name="specialization" />
            <div class="md:col-span-2 flex justify-end gap-3 pt-4 border-t border-gray-100 dark:border-gray-700">
                <x-ui.button type="button" variant="outline" size="sm" x-on:click="addOpen = false">{{ __('cancel') }}</x-ui.button>
                <x-ui.button type="submit" variant="primary" size="sm">{{ __('save') }}</x-ui.button>
            </div>
        </form>
    </x-admin.modal>

    {{-- Edit --}}
    <x-admin.modal :title="__('edit_details')" var="editOpen">
        <form :action="'{{ url('/admin/staff') }}/' + current.id" method="POST" class="grid md:grid-cols-2 gap-4">
            @csrf @method('PUT')
            <x-ui.input :label="__('firstName')" name="first_name_ar" x-model="current.first_name_ar" required />
            <x-ui.input :label="__('lastName')" name="last_name_ar" x-model="current.last_name_ar" required />
            <x-ui.input :label="__('email')" name="email" type="email" x-model="current.email" required />
            <x-ui.select :label="__('job_role')" name="role" :options="$roleOptions" x-model="current.role_key" required />
            <x-ui.input :label="__('jobTitle')" name="title" x-model="current.title" />
            <x-ui.input :label="__('phone')" name="phone" x-model="current.phone" />
            <div class="md:col-span-2">
                <x-ui.input :label="__('resetPassword')" name="password" type="password" autocomplete="new-password" :hint="__('resetPasswordHint')" />
            </div>
            <label class="md:col-span-2 flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
                <input type="checkbox" name="is_available" value="1" x-model="current.is_available"
                       class="w-4 h-4 rounded border-gray-300 text-medical-blue focus:ring-medical-blue">
                {{ __('active') }}
            </label>
            <div class="md:col-span-2 flex justify-end gap-3 pt-4 border-t border-gray-100 dark:border-gray-700">
                <x-ui.button type="button" variant="outline" size="sm" x-on:click="editOpen = false">{{ __('cancel') }}</x-ui.button>
                <x-ui.button type="submit" variant="primary" size="sm">{{ __('save_changes') }}</x-ui.button>
            </div>
        </form>
    </x-admin.modal>
</div>
@endsection
