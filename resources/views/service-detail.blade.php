@extends('layouts.public')

@section('title', $service['title'] . ' | د. محمد عوض')

@section('content')
    <section class="pt-32 pb-16 gradient-medical">
        <div class="container-custom text-center text-white">
            <div class="inline-flex items-center justify-center w-20 h-20 rounded-2xl bg-white/20 mb-6">
                @svg('lucide-' . $service['icon'], 'w-10 h-10 text-white')
            </div>
            <h1 class="text-4xl md:text-5xl font-bold mb-4">{{ $service['title'] }}</h1>
            <p class="text-xl text-white/80 max-w-3xl mx-auto">{{ $service['description'] }}</p>
        </div>
    </section>

    <section class="py-16">
        <div class="container-custom">
            <div class="grid lg:grid-cols-2 gap-12 items-center max-w-5xl mx-auto">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800 dark:text-white mb-6">ما نقدمه</h2>
                    <ul class="space-y-3 mb-8">
                        @foreach ($service['features'] as $feature)
                            <li class="flex items-center gap-3 p-4 bg-gray-50 dark:bg-gray-800 rounded-xl">
                                @svg('lucide-check-circle', 'w-5 h-5 text-green-500 flex-shrink-0')
                                <span class="text-gray-700 dark:text-gray-300">{{ $feature }}</span>
                            </li>
                        @endforeach
                    </ul>
                    <x-ui.button href="/booking" variant="primary" size="lg">
                        احجزي موعد الآن
                        <x-slot:rightIcon>@svg('lucide-arrow-left', 'w-5 h-5')</x-slot:rightIcon>
                    </x-ui.button>
                </div>
                <div class="relative aspect-square rounded-3xl overflow-hidden shadow-2xl">
                    <img src="{{ $service['image'] }}" alt="{{ $service['title'] }}" class="w-full h-full object-cover">
                    <div class="absolute inset-0 bg-gradient-to-br {{ $service['color'] }} opacity-20"></div>
                </div>
            </div>
        </div>
    </section>
@endsection
