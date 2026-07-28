@extends('layouts.admin')

@php
    $typeOptions = [
        ['value' => 'article', 'label' => __('type_article')],
        ['value' => 'video', 'label' => __('type_video')],
        ['value' => 'faq', 'label' => __('type_faq')],
        ['value' => 'success_story', 'label' => __('type_success_story')],
        ['value' => 'news', 'label' => __('type_news')],
        ['value' => 'offer', 'label' => __('type_offer')],
    ];
    $blank = ['id' => '', 'type' => 'article', 'title_ar' => '', 'title_en' => '', 'excerpt_ar' => '', 'content_ar' => '', 'image_url' => '', 'is_published' => false];
@endphp

@section('content')
<div x-data="{ open: false, mode: 'add', current: @js($blank) }">
    <x-admin.page-header :title="__('content_admin')">
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
                            <th class="px-6 py-4 text-start">{{ __('content_type') }}</th>
                            <th class="px-6 py-4 text-start">{{ __('title_label') }}</th>
                            <th class="px-6 py-4 text-start">{{ __('status') }}</th>
                            <th class="px-6 py-4 text-start">{{ __('actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse ($items as $c)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors group">
                                <td class="px-6 py-4">
                                    <span class="px-2 py-1 rounded bg-medical-blue/10 text-medical-blue text-xs font-bold">{{ __('type_' . $c->type) }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="font-medium text-gray-800 dark:text-gray-200">{{ $c->title_ar }}</p>
                                    @if ($c->excerpt_ar)
                                        <p class="text-xs text-gray-400 max-w-md truncate">{{ $c->excerpt_ar }}</p>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <x-admin.badge :color="$c->is_published ? 'green' : 'gray'">{{ $c->is_published ? __('published') : __('draft') }}</x-admin.badge>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-1">
                                        <button class="p-1.5 text-gray-400 hover:text-medical-blue"
                                                x-on:click="mode = 'edit'; current = {{ Js::from([
                                                    'id' => $c->id,
                                                    'type' => $c->type,
                                                    'title_ar' => $c->title_ar,
                                                    'title_en' => $c->title_en,
                                                    'excerpt_ar' => $c->excerpt_ar,
                                                    'content_ar' => $c->content_ar,
                                                    'image_url' => $c->image_url,
                                                    'is_published' => (bool) $c->is_published,
                                                ]) }}; open = true">@svg('lucide-pencil', 'w-[18px] h-[18px]')</button>
                                        <form method="POST" action="{{ route('admin.content.destroy', $c->id) }}" onsubmit="return confirm('{{ __('confirmDelete') }}')">
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

    {{-- Add / Edit editor --}}
    <x-admin.modal :title="__('edit_details')" var="open" max-width="max-w-3xl">
        <form method="POST"
              :action="mode === 'edit' ? '{{ url('/admin/content') }}/' + current.id : '{{ route('admin.content.store') }}'"
              class="space-y-4">
            @csrf
            <template x-if="mode === 'edit'"><input type="hidden" name="_method" value="PUT"></template>

            <div class="grid md:grid-cols-2 gap-4">
                <x-ui.select :label="__('content_type')" name="type" :options="$typeOptions" x-model="current.type" required />
                <x-ui.input :label="__('imageUrl')" name="image_url" x-model="current.image_url" />
            </div>
            <div class="grid md:grid-cols-2 gap-4">
                <x-ui.input :label="__('title_label') . ' (AR)'" name="title_ar" x-model="current.title_ar" required />
                <x-ui.input :label="__('title_label') . ' (EN)'" name="title_en" x-model="current.title_en" />
            </div>
            <x-ui.textarea :label="__('excerptLabel')" name="excerpt_ar" rows="2" x-model="current.excerpt_ar" />
            <x-ui.textarea :label="__('bodyContent')" name="content_ar" rows="5" x-model="current.content_ar" />
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
