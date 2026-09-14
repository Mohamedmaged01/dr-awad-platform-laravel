@props(['articles' => []])
@if (count($articles))
<section class="section-padding bg-gray-50 dark:bg-gray-900">
    <div class="container-custom">
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-12">
            <div>
                <span class="inline-block text-medical-blue dark:text-light-gold font-semibold mb-2">{{ __('blog') }}</span>
                <h2 class="heading-primary">{{ __('blogTitle') }}</h2>
                <p class="text-gray-600 dark:text-gray-400 mt-2">{{ __('blogSubtitle') }}</p>
            </div>
            <x-ui.button href="/blog" variant="outline">
                {{ __('viewAll') }}
                <x-slot:rightIcon>@svg('lucide-arrow-left', 'w-5 h-5')</x-slot:rightIcon>
            </x-ui.button>
        </div>

        <div class="grid md:grid-cols-3 gap-6">
            @foreach (array_slice($articles, 0, 3) as $article)
                <a href="/blog/{{ $article['id'] }}"
                   class="group bg-white dark:bg-gray-800 rounded-2xl overflow-hidden shadow-md hover:shadow-xl transition-shadow">
                    <div class="aspect-video overflow-hidden bg-gray-100 dark:bg-gray-700">
                        @if (!empty($article['image']))
                            <img src="{{ $article['image'] }}" alt="{{ $article['title'] }}"
                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-gray-300 dark:text-gray-600">
                                @svg('lucide-newspaper', 'w-12 h-12')
                            </div>
                        @endif
                    </div>
                    <div class="p-6">
                        @if (!empty($article['category']))
                            <span class="inline-block text-xs font-medium text-medical-blue bg-medical-blue/10 rounded-full px-3 py-1 mb-3">{{ $article['category'] }}</span>
                        @endif
                        <h3 class="font-bold text-lg text-gray-800 dark:text-white mb-2 line-clamp-2 group-hover:text-medical-blue transition-colors">{{ $article['title'] }}</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 line-clamp-2 mb-4">{{ $article['excerpt'] }}</p>
                        <div class="flex items-center justify-between text-xs text-gray-400">
                            <span class="flex items-center gap-1">@svg('lucide-calendar', 'w-3.5 h-3.5') {{ $article['date'] }}</span>
                            <span class="text-medical-blue font-medium flex items-center gap-1">{{ __('readMore') }} @svg('lucide-arrow-left', 'w-3.5 h-3.5')</span>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</section>
@endif
