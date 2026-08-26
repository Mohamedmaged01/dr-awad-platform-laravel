@props([
    'var' => 'rows',       // Alpine array variable in the parent x-data
    'name' => 'rows',      // form field name prefix
    'title' => '',
])
{{-- A repeatable Arabic/English row list. Empty rows are dropped server-side. --}}
<div>
    <div class="flex items-center justify-between mb-2">
        <h3 class="font-semibold text-gray-800 dark:text-white">{{ $title }}</h3>
        <button type="button" x-on:click="{{ $var }}.push({ ar: '', en: '' })"
                class="inline-flex items-center gap-1 text-sm text-medical-blue hover:underline">
            @svg('lucide-plus', 'w-4 h-4') {{ __('add_new') }}
        </button>
    </div>
    <div class="space-y-2">
        <template x-for="(row, i) in {{ $var }}" :key="i">
            <div class="flex items-center gap-2">
                <input :name="'{{ $name }}[' + i + '][ar]'" x-model="row.ar" placeholder="عربي" dir="rtl"
                       class="flex-1 px-3 py-2 text-sm border rounded-lg bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-medical-blue">
                <input :name="'{{ $name }}[' + i + '][en]'" x-model="row.en" placeholder="English" dir="ltr"
                       class="flex-1 px-3 py-2 text-sm border rounded-lg bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-medical-blue">
                <button type="button" x-on:click="{{ $var }}.splice(i, 1)" class="p-2 text-gray-400 hover:text-red-500 flex-shrink-0">@svg('lucide-trash-2', 'w-[18px] h-[18px]')</button>
            </div>
        </template>
        <p x-show="{{ $var }}.length === 0" class="text-sm text-gray-400 py-2">{{ __('noRecords') }}</p>
    </div>
</div>
