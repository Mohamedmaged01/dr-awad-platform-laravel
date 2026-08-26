@extends('layouts.admin')

@section('title', __('content_admin'))

@section('content')
@include('admin.content._nav', ['active' => 'about'])

<div x-data="{
        doctorImage: @js($doctorImage),
        qualifications: @js($qualifications ?: []),
        experience: @js($experience ?: []),
        memberships: @js($memberships ?: []),
        why: @js($why ?: []),
     }" class="max-w-4xl">
    <x-admin.page-header :title="__('aboutDoctor')" />

    <form method="POST" action="{{ route('admin.content.about.update') }}" enctype="multipart/form-data" class="space-y-6">
        @csrf @method('PUT')

        {{-- Bio + photo --}}
        <x-ui.card>
            <x-ui.card-header><h2 class="font-bold text-gray-800 dark:text-white">{{ __('doctorBio') }}</h2></x-ui.card-header>
            <x-ui.card-content class="space-y-4">
                <x-ui.textarea :label="__('doctorBio') . ' (AR)'" name="bio_ar" rows="4">{{ $bioAr }}</x-ui.textarea>
                <x-ui.textarea :label="__('doctorBio') . ' (EN)'" name="bio_en" rows="4">{{ $bioEn }}</x-ui.textarea>
                @include('admin.content._media', ['label' => __('doctorPhoto'), 'kind' => 'image', 'fileField' => 'doctor_image_file', 'urlField' => 'doctor_image_url', 'bind' => 'doctorImage', 'accept' => 'image/*'])
            </x-ui.card-content>
        </x-ui.card>

        {{-- Lists --}}
        <x-ui.card>
            <x-ui.card-content class="space-y-6">
                @include('admin.content._bilingual-list', ['var' => 'qualifications', 'name' => 'qualifications', 'title' => __('qualificationsLabel')])
                @include('admin.content._bilingual-list', ['var' => 'experience', 'name' => 'experience', 'title' => __('experienceLabel')])
                @include('admin.content._bilingual-list', ['var' => 'memberships', 'name' => 'memberships', 'title' => __('membershipsLabel')])
            </x-ui.card-content>
        </x-ui.card>

        {{-- Why choose us (icon + title + desc, bilingual) --}}
        <x-ui.card>
            <x-ui.card-header class="flex flex-row items-center justify-between">
                <h2 class="font-bold text-gray-800 dark:text-white">{{ __('whyChooseUsLabel') }}</h2>
                <x-ui.button type="button" variant="outline" size="sm" x-on:click="why.push({ icon: 'award', title_ar: '', title_en: '', desc_ar: '', desc_en: '' })">
                    <x-slot:leftIcon>@svg('lucide-plus', 'w-4 h-4')</x-slot:leftIcon>{{ __('add_new') }}
                </x-ui.button>
            </x-ui.card-header>
            <x-ui.card-content class="space-y-4">
                <template x-for="(row, i) in why" :key="i">
                    <div class="p-4 rounded-xl border border-gray-200 dark:border-gray-700 space-y-3">
                        <div class="flex items-center gap-2">
                            <select :name="'why[' + i + '][icon]'" x-model="row.icon"
                                    class="px-3 py-2 text-sm border rounded-lg bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-medical-blue">
                                @foreach (['award', 'users', 'heart', 'calendar', 'shield-check', 'star', 'clock'] as $ic)
                                    <option value="{{ $ic }}">{{ $ic }}</option>
                                @endforeach
                            </select>
                            <button type="button" x-on:click="why.splice(i, 1)" class="p-2 text-gray-400 hover:text-red-500 ms-auto">@svg('lucide-trash-2', 'w-[18px] h-[18px]')</button>
                        </div>
                        <div class="grid md:grid-cols-2 gap-2">
                            <input :name="'why[' + i + '][title_ar]'" x-model="row.title_ar" placeholder="العنوان (عربي)" dir="rtl" class="px-3 py-2 text-sm border rounded-lg bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-medical-blue">
                            <input :name="'why[' + i + '][title_en]'" x-model="row.title_en" placeholder="Title (English)" dir="ltr" class="px-3 py-2 text-sm border rounded-lg bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-medical-blue">
                            <input :name="'why[' + i + '][desc_ar]'" x-model="row.desc_ar" placeholder="الوصف (عربي)" dir="rtl" class="px-3 py-2 text-sm border rounded-lg bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-medical-blue">
                            <input :name="'why[' + i + '][desc_en]'" x-model="row.desc_en" placeholder="Description (English)" dir="ltr" class="px-3 py-2 text-sm border rounded-lg bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-medical-blue">
                        </div>
                    </div>
                </template>
                <p x-show="why.length === 0" class="text-sm text-gray-400">{{ __('noRecords') }}</p>
            </x-ui.card-content>
        </x-ui.card>

        <div class="flex justify-end">
            <x-ui.button type="submit" variant="primary" size="sm">{{ __('save_changes') }}</x-ui.button>
        </div>
    </form>
</div>
@endsection
