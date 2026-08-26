@props([
    'label' => '',
    'fileField' => 'image_file',
    'urlField' => 'image_url',
    'bind' => 'current.image_url',   // Alpine expression holding the current URL
    'accept' => 'image/*',
    'kind' => 'image',               // 'image' or 'video'
])
{{-- Upload a file OR paste a URL. The uploaded file wins server-side. --}}
<div class="w-full">
    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">{{ $label }}</label>

    <template x-if="{{ $bind }}">
        <div class="mb-2">
            @if ($kind === 'image')
                <img :src="{{ $bind }}" alt="" class="h-24 w-auto rounded-lg object-cover border border-gray-200 dark:border-gray-700">
            @else
                <p class="text-xs text-gray-500 truncate" x-text="{{ $bind }}"></p>
            @endif
        </div>
    </template>

    <input type="file" name="{{ $fileField }}" accept="{{ $accept }}"
           class="block w-full text-sm text-gray-600 dark:text-gray-300 file:me-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-medical-blue/10 file:text-medical-blue hover:file:bg-medical-blue/20 cursor-pointer">

    <input type="text" name="{{ $urlField }}" x-model="{{ $bind }}" placeholder="{{ __('orPasteUrl') }}"
           class="mt-2 w-full px-4 py-2.5 text-sm border rounded-lg bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700 text-gray-900 dark:text-white placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-medical-blue">
</div>
