@extends('layouts.admin')

@section('content')
<div x-data="{ items: {{ Js::from($messages) }}, selected: 0 }">
    <x-admin.page-header :title="__('messages_admin')" />

    <div class="grid lg:grid-cols-3 gap-6 h-[calc(100vh-14rem)]">
        {{-- List --}}
        <x-ui.card class="lg:col-span-1 overflow-hidden flex flex-col">
            <div class="p-4 border-b border-gray-100 dark:border-gray-700">
                <x-ui.input name="search" :placeholder="__('search_placeholder')">
                    <x-slot:leftIcon>@svg('lucide-search', 'w-[18px] h-[18px]')</x-slot:leftIcon>
                </x-ui.input>
            </div>
            <div class="overflow-y-auto flex-1">
                <template x-for="(m, i) in items" :key="i">
                    <button @click="selected = i"
                            class="w-full text-start p-4 border-b border-gray-100 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors"
                            :class="selected === i && 'ring-2 ring-inset ring-medical-blue bg-medical-blue/5'">
                        <div class="flex items-center justify-between mb-1">
                            <span class="font-medium text-gray-800 dark:text-white flex items-center gap-2">
                                <span x-show="m.unread" class="w-2 h-2 rounded-full bg-medical-blue"></span>
                                <span x-text="m.name"></span>
                            </span>
                            <span class="text-xs text-gray-400" x-text="m.time"></span>
                        </div>
                        <p class="text-sm text-gray-500 truncate" x-text="m.subject"></p>
                    </button>
                </template>
            </div>
        </x-ui.card>

        {{-- Detail --}}
        <x-ui.card class="lg:col-span-2 flex flex-col">
            <template x-if="items[selected]">
                <div class="flex flex-col h-full">
                    <div class="p-6 border-b border-gray-100 dark:border-gray-700">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="font-bold text-gray-800 dark:text-white" x-text="items[selected].subject"></h3>
                                <p class="text-sm text-gray-500 flex items-center gap-3 mt-1">
                                    <span class="flex items-center gap-1">@svg('lucide-mail', 'w-3.5 h-3.5') <span x-text="items[selected].email"></span></span>
                                    <span class="flex items-center gap-1">@svg('lucide-phone', 'w-3.5 h-3.5') <span x-text="items[selected].phone"></span></span>
                                </p>
                            </div>
                            <form method="POST" :action="'{{ url('/admin/messages') }}/' + items[selected].id"
                                  onsubmit="return confirm('{{ __('confirmDelete') }}')">
                                @csrf @method('DELETE')
                                <button type="submit" class="p-1.5 text-gray-400 hover:text-red-500">@svg('lucide-trash-2', 'w-[18px] h-[18px]')</button>
                            </form>
                        </div>
                    </div>
                    <div class="p-6 flex-1 overflow-y-auto">
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-xl p-4 text-gray-700 dark:text-gray-300" x-text="items[selected].message"></div>
                    </div>
                    <form method="POST" :action="'{{ url('/admin/messages') }}/' + items[selected].id + '/reply'" class="p-4 border-t border-gray-100 dark:border-gray-700">
                        @csrf @method('PATCH')
                        <x-ui.textarea name="reply" rows="2" :placeholder="__('yourMessage')" />
                        <div class="flex justify-end mt-3">
                            <x-ui.button type="submit" variant="primary" size="sm">
                                <x-slot:leftIcon>@svg('lucide-send', 'w-[18px] h-[18px]')</x-slot:leftIcon>
                                {{ __('send') }}
                            </x-ui.button>
                        </div>
                    </form>
                </div>
            </template>
        </x-ui.card>
    </div>
</div>
@endsection
