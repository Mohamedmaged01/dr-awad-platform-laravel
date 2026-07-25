@props([
    'title' => '',
    'var' => 'modalOpen',   // name of the Alpine boolean in the parent x-data
    'maxWidth' => 'max-w-2xl',
])
{{-- Reusable admin modal. Parent supplies x-data with a boolean named by :var. --}}
<div x-show="{{ $var }}" x-cloak
     class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50 p-4"
     x-transition.opacity
     @keydown.escape.window="{{ $var }} = false">
    <div @click.outside="{{ $var }} = false"
         class="w-full {{ $maxWidth }} max-h-[90vh] overflow-y-auto bg-white dark:bg-gray-800 rounded-2xl shadow-md animate-fade-in">
        <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center sticky top-0 bg-white dark:bg-gray-800 z-10">
            <h3 class="text-lg font-bold text-gray-800 dark:text-white">{{ $title }}</h3>
            <button type="button" @click="{{ $var }} = false"
                    class="p-1 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                @svg('lucide-x', 'w-5 h-5')
            </button>
        </div>
        <div class="p-6">
            {{ $slot }}
        </div>
        @isset($footer)
            <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700 flex justify-end gap-3">
                {{ $footer }}
            </div>
        @endisset
    </div>
</div>
