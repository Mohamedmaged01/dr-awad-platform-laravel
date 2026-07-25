@extends('layouts.admin')

@php $contact = config('clinic.contact'); @endphp

@section('content')
<div x-data="{ tab: 'general' }" class="max-w-5xl">
    <x-admin.page-header :title="__('settings')">
        <x-slot:actions>
            <x-ui.button variant="primary" size="sm">{{ __('save_changes') }}</x-ui.button>
        </x-slot:actions>
    </x-admin.page-header>

    <div class="grid lg:grid-cols-4 gap-6">
        {{-- Tabs --}}
        <x-ui.card class="lg:col-span-1 h-fit">
            <x-ui.card-content class="p-2">
                @foreach ([
                    'general' => ['settings', __('settings')],
                    'seo'     => ['search', 'SEO'],
                    'security'=> ['lock', __('permissions')],
                ] as $key => $meta)
                    <button @click="tab = '{{ $key }}'"
                            :class="tab === '{{ $key }}' ? 'bg-medical-blue text-white' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700'"
                            class="w-full flex items-center gap-3 px-4 py-3 rounded-lg text-start transition-colors mb-1">
                        @svg('lucide-' . $meta[0], 'w-[18px] h-[18px]')
                        <span>{{ $meta[1] }}</span>
                    </button>
                @endforeach
            </x-ui.card-content>
        </x-ui.card>

        {{-- Panels --}}
        <div class="lg:col-span-3 space-y-6">
            <x-ui.card x-show="tab === 'general'">
                <x-ui.card-header><h2 class="font-bold text-gray-800 dark:text-white">{{ __('contactInfo') }}</h2></x-ui.card-header>
                <x-ui.card-content class="grid md:grid-cols-2 gap-4">
                    <x-ui.input label="اسم الموقع (AR)" name="site_name_ar" value="د. محمد عوض" />
                    <x-ui.input label="Site Name (EN)" name="site_name_en" value="Dr. Mohamed Awad" />
                    <x-ui.input :label="__('email')" name="email" :value="$contact['email']" />
                    <x-ui.input :label="__('phone')" name="phone" :value="$contact['phone_display']" />
                    <x-ui.input label="واتساب" name="whatsapp" :value="$contact['whatsapp']" />
                </x-ui.card-content>
            </x-ui.card>

            <x-ui.card x-show="tab === 'seo'" x-cloak>
                <x-ui.card-header><h2 class="font-bold text-gray-800 dark:text-white">SEO</h2></x-ui.card-header>
                <x-ui.card-content class="space-y-4">
                    <x-ui.input label="Meta Title" name="meta_title" :value="__('metaTitle')" />
                    <x-ui.textarea label="Meta Description" name="meta_desc" rows="3">{{ __('metaDescription') }}</x-ui.textarea>
                </x-ui.card-content>
            </x-ui.card>

            <x-ui.card x-show="tab === 'security'" x-cloak class="border border-red-100 dark:border-red-900/40">
                <x-ui.card-header><h2 class="font-bold text-red-600">{{ __('permissions') }}</h2></x-ui.card-header>
                <x-ui.card-content>
                    <a href="/admin/settings/permissions" class="text-medical-blue hover:underline flex items-center gap-2">
                        @svg('lucide-shield-check', 'w-[18px] h-[18px]')
                        {{ __('permissions') }}
                    </a>
                </x-ui.card-content>
            </x-ui.card>
        </div>
    </div>
</div>
@endsection
