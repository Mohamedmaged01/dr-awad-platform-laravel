@extends('layouts.public')

@section('title', 'فيديوهات طبية | د. محمد عوض')

@section('content')
    {{-- Hero Section --}}
    <section class="pt-32 pb-16 gradient-medical">
        <div class="container-custom text-center text-white">
            <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-white/20 mb-6">
                @svg('lucide-play', 'w-10 h-10 text-white')
            </div>
            <h1 class="text-4xl md:text-5xl font-bold mb-4">فيديوهات طبية</h1>
            <p class="text-xl text-white/80 max-w-2xl mx-auto">
                مجموعة من الفيديوهات التعليمية والتوعوية من د. محمد عوض حول أهم المواضيع الطبية في مجال النساء والتوليد والحقن المجهري
            </p>
        </div>
    </section>

    {{-- Categories Filter --}}
    <section class="py-6 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 sticky top-20 z-30">
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

    {{-- Videos Grid --}}
    <section class="py-16">
        <div class="container-custom">
            <div class="grid md:grid-cols-2 gap-8">
                @foreach ($videos as $video)
                    <article class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg overflow-hidden card-hover group">
                        {{-- Video Embed --}}
                        <div class="relative aspect-video">
                            <iframe src="https://www.youtube.com/embed/{{ $video['id'] }}"
                                    title="{{ $video['title'] }}"
                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                    allowfullscreen
                                    class="absolute inset-0 w-full h-full"></iframe>
                        </div>

                        {{-- Video Info --}}
                        <div class="p-6">
                            <div class="flex items-center gap-3 mb-3">
                                <span class="px-3 py-1 bg-medical-blue/10 text-medical-blue rounded-full text-sm font-medium">
                                    {{ $video['category'] }}
                                </span>
                                <div class="flex items-center gap-1 text-sm text-gray-500">
                                    @svg('lucide-clock', 'w-3.5 h-3.5')
                                    {{ $video['duration'] }}
                                </div>
                            </div>

                            <h2 class="text-xl font-bold text-gray-800 dark:text-white mb-2">{{ $video['title'] }}</h2>
                            <p class="text-gray-600 dark:text-gray-400 mb-4 line-clamp-2">{{ $video['description'] }}</p>

                            <div class="flex items-center justify-between pt-4 border-t border-gray-100 dark:border-gray-700">
                                <div class="flex items-center gap-4 text-sm text-gray-500">
                                    <span class="flex items-center gap-1">
                                        @svg('lucide-eye', 'w-3.5 h-3.5')
                                        {{ $video['views'] }} مشاهدة
                                    </span>
                                    <span class="flex items-center gap-1">
                                        @svg('lucide-calendar', 'w-3.5 h-3.5')
                                        {{ \Carbon\Carbon::parse($video['date'])->locale('ar_EG')->isoFormat('D/M/YYYY') }}
                                    </span>
                                </div>
                                <a href="https://www.youtube.com/watch?v={{ $video['id'] }}" target="_blank" rel="noopener noreferrer"
                                   class="text-medical-blue font-medium hover:underline flex items-center gap-1">
                                    مشاهدة على يوتيوب
                                    @svg('lucide-play', 'w-3.5 h-3.5')
                                </a>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Subscribe CTA --}}
    <section class="py-16 bg-gradient-to-r from-red-600 to-red-700">
        <div class="container-custom text-center text-white">
            <div class="max-w-2xl mx-auto">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-white/20 mb-6">
                    <svg class="w-8 h-8" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
                    </svg>
                </div>
                <h2 class="text-3xl font-bold mb-4">اشترك في قناتنا على يوتيوب</h2>
                <p class="text-white/80 text-lg mb-8">
                    تابعونا للحصول على أحدث الفيديوهات التعليمية والنصائح الطبية المفيدة
                </p>
                <a href="{{ config('clinic.contact.youtube') }}" target="_blank" rel="noopener noreferrer"
                   class="inline-flex items-center gap-3 bg-white text-red-600 px-8 py-4 rounded-full font-bold text-lg hover:bg-gray-100 transition-colors">
                    <svg class="w-6 h-6" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
                    </svg>
                    اشترك الآن
                </a>
            </div>
        </div>
    </section>

    {{-- More Videos Section --}}
    <section class="py-16 bg-gray-50 dark:bg-gray-900">
        <div class="container-custom">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-800 dark:text-white mb-4">هل لديكِ سؤال؟</h2>
                <p class="text-gray-600 dark:text-gray-400 text-lg max-w-2xl mx-auto">
                    إذا كان لديكِ أي استفسار طبي، لا تترددي في التواصل معنا أو حجز موعد للاستشارة
                </p>
            </div>
            <div class="flex flex-wrap justify-center gap-4">
                <a href="/booking" class="bg-medical-blue text-white px-8 py-4 rounded-lg font-semibold hover:bg-medical-blue-dark transition-colors">
                    احجزي موعد استشارة
                </a>
                <a href="https://wa.me/{{ config('clinic.contact.whatsapp') }}" target="_blank" rel="noopener noreferrer"
                   class="bg-green-500 text-white px-8 py-4 rounded-lg font-semibold hover:bg-green-600 transition-colors">
                    تواصلي عبر واتساب
                </a>
            </div>
        </div>
    </section>
@endsection
