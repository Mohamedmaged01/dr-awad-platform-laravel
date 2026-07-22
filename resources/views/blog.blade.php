@extends('layouts.public')

@section('title', 'المدونة الطبية | د. محمد عوض')

@section('content')
    {{-- Hero --}}
    <section class="pt-32 pb-16 gradient-medical">
        <div class="container-custom text-center text-white">
            <h1 class="text-4xl md:text-5xl font-bold mb-4">المدونة الطبية</h1>
            <p class="text-xl text-white/80 max-w-2xl mx-auto">مقالات طبية موثوقة ونصائح صحية من د. محمد عوض</p>
        </div>
    </section>

    {{-- Categories --}}
    <section class="py-8 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 sticky top-20 z-30">
        <div class="container-custom">
            <div class="flex gap-3 overflow-x-auto pb-2">
                @foreach ($categories as $index => $category)
                    <button @class([
                        'px-5 py-2 rounded-full whitespace-nowrap transition-colors',
                        'bg-medical-blue text-white' => $index === 0,
                        'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-medical-blue/10' => $index !== 0,
                    ])>{{ $category }}</button>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Articles Grid --}}
    <section class="py-16">
        <div class="container-custom">
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach ($articles as $article)
                    <article class="bg-white dark:bg-gray-800 rounded-2xl shadow-md overflow-hidden card-hover">
                        <div class="aspect-video bg-gradient-to-br from-medical-blue to-medical-blue-dark flex items-center justify-center">
                            <span class="text-6xl text-white/20">📄</span>
                        </div>
                        <div class="p-6">
                            <div class="flex items-center gap-3 mb-3">
                                <span class="px-3 py-1 bg-medical-blue/10 text-medical-blue rounded-full text-sm font-medium">
                                    {{ $article['category'] }}
                                </span>
                                <div class="flex items-center gap-1 text-sm text-gray-500">
                                    @svg('lucide-clock', 'w-3.5 h-3.5')
                                    {{ $article['readTime'] }}
                                </div>
                            </div>
                            <h2 class="text-xl font-bold text-gray-800 dark:text-white mb-3 line-clamp-2">{{ $article['title'] }}</h2>
                            <p class="text-gray-600 dark:text-gray-400 mb-4 line-clamp-3">{{ $article['excerpt'] }}</p>
                            <div class="flex items-center justify-between pt-4 border-t border-gray-100 dark:border-gray-700">
                                <div class="flex items-center gap-2 text-sm text-gray-500">
                                    @svg('lucide-user', 'w-3.5 h-3.5')
                                    {{ $article['author'] }}
                                </div>
                                <div class="flex items-center gap-2 text-sm text-gray-500">
                                    @svg('lucide-calendar', 'w-3.5 h-3.5')
                                    {{ \Carbon\Carbon::parse($article['date'])->locale('ar_EG')->isoFormat('D/M/YYYY') }}
                                </div>
                            </div>
                            <a href="/blog/{{ $article['id'] }}" class="mt-4 flex items-center gap-2 text-medical-blue font-medium hover:underline">
                                اقرأ المزيد
                                @svg('lucide-arrow-left', 'w-4 h-4')
                            </a>
                        </div>
                    </article>
                @endforeach
            </div>

            {{-- Pagination --}}
            <div class="flex justify-center mt-12">
                <div class="flex gap-2">
                    <button class="px-4 py-2 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800">السابق</button>
                    <button class="px-4 py-2 bg-medical-blue text-white rounded-lg">1</button>
                    <button class="px-4 py-2 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800">2</button>
                    <button class="px-4 py-2 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800">3</button>
                    <button class="px-4 py-2 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800">التالي</button>
                </div>
            </div>
        </div>
    </section>

    {{-- Newsletter --}}
    <section class="py-16 bg-gray-50 dark:bg-gray-900">
        <div class="container-custom text-center">
            <h2 class="text-2xl font-bold text-gray-800 dark:text-white mb-4">اشتركي في نشرتنا البريدية</h2>
            <p class="text-gray-600 dark:text-gray-400 mb-6 max-w-xl mx-auto">
                احصلي على أحدث المقالات الطبية والنصائح الصحية مباشرة في بريدك الإلكتروني
            </p>
            <form class="flex flex-col sm:flex-row gap-3 max-w-md mx-auto" onsubmit="return false">
                <input type="email" placeholder="بريدك الإلكتروني"
                       class="flex-1 px-4 py-3 rounded-lg border border-gray-200 dark:border-gray-700 dark:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-medical-blue">
                <button type="submit" class="px-6 py-3 bg-medical-blue text-white rounded-lg font-semibold hover:bg-medical-blue-dark transition-colors">
                    اشتراك
                </button>
            </form>
        </div>
    </section>
@endsection
