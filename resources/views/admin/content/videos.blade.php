@extends('layouts.admin')

@section('title', __('content_admin'))

@php
    $blank = ['id' => '', 'title_ar' => '', 'title_en' => '', 'category' => '', 'duration' => '', 'views_label' => '', 'excerpt_ar' => '', 'video_url' => '', 'image_url' => '', 'is_published' => true];
@endphp

@section('content')
@include('admin.content._nav', ['active' => 'videos'])

<div x-data="{ open: false, mode: 'add', current: @js($blank) }">
    <x-admin.page-header :title="__('videos')">
        <x-slot:actions>
            <x-ui.button variant="primary" size="sm" x-on:click="mode = 'add'; current = @js($blank); open = true">
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
                            <th class="px-6 py-4 text-start">{{ __('title_label') }}</th>
                            <th class="px-6 py-4 text-start">{{ __('category') }}</th>
                            <th class="px-6 py-4 text-start">{{ __('status') }}</th>
                            <th class="px-6 py-4 text-start">{{ __('actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse ($items as $c)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <span class="w-12 h-12 rounded-lg bg-medical-blue/10 text-medical-blue flex items-center justify-center flex-shrink-0">@svg('lucide-play', 'w-5 h-5')</span>
                                        <div class="min-w-0">
                                            <p class="font-medium text-gray-800 dark:text-gray-200 truncate">{{ $c->title_ar }}</p>
                                            <p class="text-xs text-gray-400 truncate">{{ $c->video_url }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-gray-600 dark:text-gray-400">{{ $c->meta['category'] ?? '—' }}</td>
                                <td class="px-6 py-4">
                                    <x-admin.badge :color="$c->is_published ? 'green' : 'gray'">{{ $c->is_published ? __('published') : __('draft') }}</x-admin.badge>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-1">
                                        <button class="p-1.5 text-gray-400 hover:text-medical-blue"
                                                x-on:click="mode = 'edit'; current = {{ Js::from([
                                                    'id' => $c->id, 'title_ar' => $c->title_ar, 'title_en' => $c->title_en,
                                                    'category' => $c->meta['category'] ?? '', 'duration' => $c->meta['duration'] ?? '',
                                                    'views_label' => $c->meta['views_label'] ?? '', 'excerpt_ar' => $c->excerpt_ar,
                                                    'video_url' => $c->video_url, 'image_url' => $c->image_url, 'is_published' => (bool) $c->is_published,
                                                ]) }}; open = true">@svg('lucide-pencil', 'w-[18px] h-[18px]')</button>
                                        <form method="POST" action="{{ route('admin.content.videos.destroy', $c->id) }}" onsubmit="return confirm('{{ __('confirmDelete') }}')">
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

    <x-admin.modal :title="__('edit_details')" var="open" max-width="max-w-3xl">
        <form method="POST" enctype="multipart/form-data"
              :action="mode === 'edit' ? '{{ url('/admin/content/videos') }}/' + current.id : '{{ route('admin.content.videos.store') }}'"
              class="space-y-4">
            @csrf
            <input type="hidden" name="_method" :value="mode === 'edit' ? 'PUT' : 'POST'">

            <div class="grid md:grid-cols-2 gap-4">
                <x-ui.input :label="__('title_label') . ' (AR)'" name="title_ar" x-model="current.title_ar" required />
                <x-ui.input :label="__('title_label') . ' (EN)'" name="title_en" x-model="current.title_en" />
            </div>
            <div class="grid md:grid-cols-3 gap-4">
                <x-ui.input :label="__('category')" name="category" x-model="current.category" />
                <x-ui.input :label="__('duration')" name="duration" x-model="current.duration" placeholder="10:25" />
                <x-ui.input :label="__('viewsLabel')" name="views_label" x-model="current.views_label" placeholder="15K" />
            </div>
            <x-ui.textarea :label="__('excerptLabel')" name="excerpt_ar" rows="2" x-model="current.excerpt_ar" />

            @include('admin.content._media', ['label' => __('videoSource'), 'kind' => 'video', 'fileField' => 'video_file', 'urlField' => 'video_url', 'bind' => 'current.video_url', 'accept' => 'video/mp4,video/webm,video/ogg'])
            <p class="-mt-2 text-xs text-gray-500">{{ __('videoSourceHint') }}</p>

            @include('admin.content._media', ['label' => __('thumbnail'), 'kind' => 'image', 'fileField' => 'image_file', 'urlField' => 'image_url', 'bind' => 'current.image_url', 'accept' => 'image/*'])

            <label class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
                <input type="checkbox" name="is_published" value="1" x-model="current.is_published"
                       class="w-4 h-4 rounded border-gray-300 text-medical-blue focus:ring-medical-blue">
                {{ __('published') }}
            </label>

            <div class="flex justify-end gap-3 pt-4 border-t border-gray-100 dark:border-gray-700">
                <x-ui.button type="button" variant="outline" size="sm" x-on:click="open = false">{{ __('cancel') }}</x-ui.button>
                <x-ui.button type="submit" variant="primary" size="sm">{{ __('save_changes') }}</x-ui.button>
            </div>
        </form>
    </x-admin.modal>
</div>
@endsection
