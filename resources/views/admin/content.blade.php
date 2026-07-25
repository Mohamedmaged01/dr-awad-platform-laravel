@extends('layouts.admin')

@section('content')
<div x-data="{ editorOpen: false }">
    <x-admin.page-header :title="__('content_admin')">
        <x-slot:actions>
            <x-ui.button variant="primary" size="sm" x-on:click="editorOpen = true">
                <x-slot:leftIcon>@svg('lucide-plus', 'w-[18px] h-[18px]')</x-slot:leftIcon>
                {{ __('add_new') }}
            </x-ui.button>
        </x-slot:actions>
    </x-admin.page-header>

    <x-ui.card>
        <x-ui.card-content class="p-0">
            <div class="overflow-x-auto">
                <table class="w-full text-start">
                    <thead class="bg-gray-50 dark:bg-gray-700/50">
                        <tr class="text-sm font-semibold text-gray-600 dark:text-gray-300">
                            <th class="px-6 py-4 text-start">{{ __('services') }}</th>
                            <th class="px-6 py-4 text-start">Key</th>
                            <th class="px-6 py-4 text-start">{{ __('details') }}</th>
                            <th class="px-6 py-4 text-start">{{ __('actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach ($content as $c)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors group">
                                <td class="px-6 py-4">
                                    <span class="px-2 py-1 rounded bg-medical-blue/10 text-medical-blue text-xs font-bold uppercase">{{ $c['page'] }}</span>
                                    <span class="text-xs text-gray-500 ms-2">{{ $c['section'] }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="font-mono text-sm text-gray-800 dark:text-gray-200">{{ $c['key'] }}</p>
                                    <span class="text-xs text-gray-400">{{ $c['type'] }}</span>
                                </td>
                                <td class="px-6 py-4 text-gray-600 dark:text-gray-400 max-w-xs truncate">{{ $c['preview'] }}</td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                        <button class="p-1.5 text-gray-400 hover:text-medical-blue" x-on:click="editorOpen = true">@svg('lucide-pencil', 'w-[18px] h-[18px]')</button>
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

    <x-admin.modal :title="__('edit_details')" var="editorOpen" max-width="max-w-3xl">
        <form class="space-y-4" @submit.prevent="editorOpen = false">
            <div class="grid md:grid-cols-3 gap-4">
                <x-ui.input label="Page" name="page" />
                <x-ui.input label="Section" name="section" />
                <x-ui.input label="Key" name="key" />
            </div>
            <div class="grid md:grid-cols-2 gap-4">
                <x-ui.textarea label="العربية (AR)" name="value_ar" rows="4" />
                <x-ui.textarea label="English (EN)" name="value_en" rows="4" />
            </div>
        </form>
        <x-slot:footer>
            <x-ui.button variant="outline" size="sm" x-on:click="editorOpen = false">{{ __('cancel') }}</x-ui.button>
            <x-ui.button variant="primary" size="sm" x-on:click="editorOpen = false">{{ __('save_changes') }}</x-ui.button>
        </x-slot:footer>
    </x-admin.modal>
</div>
@endsection
