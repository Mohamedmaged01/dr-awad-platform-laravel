@extends('layouts.public')

@section('title', $article['title'] . ' | د. محمد عوض')

@section('content')
    <section class="pt-32 pb-16 gradient-medical">
        <div class="container-custom text-center text-white max-w-3xl">
            <span class="inline-block px-3 py-1 bg-white/20 rounded-full text-sm font-medium mb-4">{{ $article['category'] }}</span>
            <h1 class="text-3xl md:text-4xl font-bold mb-4">{{ $article['title'] }}</h1>
            <div class="flex items-center justify-center gap-6 text-white/80 text-sm">
                <span class="flex items-center gap-2">@svg('lucide-user', 'w-4 h-4') {{ $article['author'] }}</span>
                <span class="flex items-center gap-2">@svg('lucide-calendar', 'w-4 h-4') {{ \Carbon\Carbon::parse($article['date'])->locale('ar_EG')->isoFormat('D/M/YYYY') }}</span>
                <span class="flex items-center gap-2">@svg('lucide-clock', 'w-4 h-4') {{ $article['readTime'] }}</span>
            </div>
        </div>
    </section>

    <section class="py-16">
        <div class="container-custom">
            <x-ui.card class="max-w-3xl mx-auto">
                <x-ui.card-content class="p-8">
                    <p class="text-lg text-gray-600 dark:text-gray-400 leading-relaxed mb-8">{{ $article['excerpt'] }}</p>
                    <p class="text-gray-500 dark:text-gray-500 mb-8">المقال الكامل قيد الإعداد.</p>
                    <x-ui.button href="/blog" variant="outline">
                        <x-slot:leftIcon>@svg('lucide-arrow-right', 'w-[18px] h-[18px]')</x-slot:leftIcon>
                        العودة للمدونة
                    </x-ui.button>
                </x-ui.card-content>
            </x-ui.card>
        </div>
    </section>
@endsection
