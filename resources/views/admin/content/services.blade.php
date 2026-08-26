@extends('layouts.admin')

@section('title', __('content_admin'))

@php
    $blank = ['id' => '', 'name_ar' => '', 'name_en' => '', 'description_ar' => '', 'description_en' => '', 'features' => '', 'icon' => 'stethoscope', 'color' => 'from-medical-blue to-cyan-500', 'price' => '', 'sort_order' => 0, 'image_url' => '', 'is_active' => true, 'is_featured' => false];
    $iconOptions = collect(['baby', 'microscope', 'activity', 'stethoscope', 'heart', 'users', 'award', 'calendar', 'shield-check'])
        ->map(fn ($i) => ['value' => $i, 'label' => $i])->all();
    $colorOptions = [
        ['value' => 'from-pink-500 to-rose-500', 'label' => 'Pink'],
        ['value' => 'from-blue-500 to-cyan-500', 'label' => 'Blue'],
        ['value' => 'from-emerald-500 to-teal-500', 'label' => 'Green'],
        ['value' => 'from-purple-500 to-violet-500', 'label' => 'Purple'],
        ['value' => 'from-amber-500 to-orange-500', 'label' => 'Amber'],
        ['value' => 'from-medical-blue to-cyan-500', 'label' => 'Medical'],
    ];
@endphp

@section('content')
@include('admin.content._nav', ['active' => 'services'])

<div x-data="{ open: false, mode: 'add', current: @js($blank) }">
    <x-admin.page-header :title="__('services')">
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
                            <th class="px-6 py-4 text-start">{{ __('serviceLabel') }}</th>
                            <th class="px-6 py-4 text-start">{{ __('order') }}</th>
                            <th class="px-6 py-4 text-start">{{ __('status') }}</th>
                            <th class="px-6 py-4 text-start">{{ __('actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse ($items as $s)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <span class="w-10 h-10 rounded-xl bg-gradient-to-r {{ $s->color ?? 'from-medical-blue to-cyan-500' }} text-white flex items-center justify-center flex-shrink-0">@svg('lucide-' . ($s->icon ?: 'stethoscope'), 'w-5 h-5')</span>
                                        <div class="min-w-0">
                                            <p class="font-medium text-gray-800 dark:text-gray-200 truncate">{{ $s->name_ar }}</p>
                                            @if ($s->name_en)<p class="text-xs text-gray-400 truncate">{{ $s->name_en }}</p>@endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-gray-600 dark:text-gray-400">{{ $s->sort_order }}</td>
                                <td class="px-6 py-4">
                                    <x-admin.badge :color="$s->is_active ? 'green' : 'gray'">{{ $s->is_active ? __('active') : __('draft') }}</x-admin.badge>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-1">
                                        <button class="p-1.5 text-gray-400 hover:text-medical-blue"
                                                x-on:click="mode = 'edit'; current = {{ Js::from([
                                                    'id' => $s->id, 'name_ar' => $s->name_ar, 'name_en' => $s->name_en,
                                                    'description_ar' => $s->description_ar, 'description_en' => $s->description_en,
                                                    'features' => implode("\n", (array) ($s->features ?? [])),
                                                    'icon' => $s->icon ?: 'stethoscope', 'color' => $s->color ?: 'from-medical-blue to-cyan-500',
                                                    'price' => $s->price, 'sort_order' => $s->sort_order, 'image_url' => $s->image_url,
                                                    'is_active' => (bool) $s->is_active, 'is_featured' => (bool) $s->is_featured,
                                                ]) }}; open = true">@svg('lucide-pencil', 'w-[18px] h-[18px]')</button>
                                        <form method="POST" action="{{ route('admin.content.services.destroy', $s->id) }}" onsubmit="return confirm('{{ __('confirmDelete') }}')">
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
              :action="mode === 'edit' ? '{{ url('/admin/content/services') }}/' + current.id : '{{ route('admin.content.services.store') }}'"
              class="space-y-4">
            @csrf
            <input type="hidden" name="_method" :value="mode === 'edit' ? 'PUT' : 'POST'">

            <div class="grid md:grid-cols-2 gap-4">
                <x-ui.input :label="__('serviceLabel') . ' (AR)'" name="name_ar" x-model="current.name_ar" required />
                <x-ui.input :label="__('serviceLabel') . ' (EN)'" name="name_en" x-model="current.name_en" />
            </div>
            <x-ui.textarea :label="__('descriptionLabel') . ' (AR)'" name="description_ar" rows="3" x-model="current.description_ar" />
            <x-ui.textarea :label="__('descriptionLabel') . ' (EN)'" name="description_en" rows="3" x-model="current.description_en" />
            <x-ui.textarea :label="__('featuresLabel')" name="features" rows="4" x-model="current.features" :placeholder="__('featuresHint')" />

            <div class="grid md:grid-cols-3 gap-4">
                <x-ui.select :label="__('icon')" name="icon" :options="$iconOptions" x-model="current.icon" />
                <x-ui.select :label="__('color')" name="color" :options="$colorOptions" x-model="current.color" />
                <x-ui.input :label="__('order')" name="sort_order" type="number" min="0" x-model="current.sort_order" />
            </div>

            @include('admin.content._media', ['label' => __('coverImage'), 'kind' => 'image'])

            <div class="flex flex-wrap items-center gap-6 pt-2">
                <label class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
                    <input type="checkbox" name="is_active" value="1" x-model="current.is_active" class="w-4 h-4 rounded border-gray-300 text-medical-blue focus:ring-medical-blue">
                    {{ __('active') }}
                </label>
                <label class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
                    <input type="checkbox" name="is_featured" value="1" x-model="current.is_featured" class="w-4 h-4 rounded border-gray-300 text-medical-blue focus:ring-medical-blue">
                    {{ __('featured') }}
                </label>
            </div>

            <div class="flex justify-end gap-3 pt-4 border-t border-gray-100 dark:border-gray-700">
                <x-ui.button type="button" variant="outline" size="sm" x-on:click="open = false">{{ __('cancel') }}</x-ui.button>
                <x-ui.button type="submit" variant="primary" size="sm">{{ __('save_changes') }}</x-ui.button>
            </div>
        </form>
    </x-admin.modal>
</div>
@endsection
