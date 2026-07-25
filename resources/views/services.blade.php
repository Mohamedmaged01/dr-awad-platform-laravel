@extends('layouts.public')

@section('title', __('servicesEyebrow') . ' | ' . __('heroTitle'))

@section('content')
    {{-- Hero Section --}}
    <section class="pt-32 pb-16 gradient-medical">
        <div class="container-custom text-center text-white">
            <h1 class="text-4xl md:text-5xl font-bold mb-6">{{ __('servicesEyebrow') }}</h1>
            <p class="text-xl text-white/80 max-w-3xl mx-auto">
                {{ __('servicesPageSubtitle') }}
            </p>
        </div>
    </section>

    {{-- Services Grid --}}
    <section class="py-16">
        <div class="container-custom">
            <div class="space-y-16">
                @foreach ($services as $index => $service)
                    <div class="grid lg:grid-cols-2 gap-12 items-center">
                        <div class="{{ $index % 2 === 1 ? 'lg:order-2' : '' }}">
                            <div class="w-16 h-16 rounded-2xl bg-gradient-to-r {{ $service['color'] }} flex items-center justify-center mb-6">
                                @svg('lucide-' . $service['icon'], 'w-8 h-8 text-white')
                            </div>
                            <h2 class="text-3xl font-bold text-gray-800 dark:text-white mb-4">{{ $service['title'] }}</h2>
                            <p class="text-gray-600 dark:text-gray-400 text-lg mb-6 leading-relaxed">{{ $service['description'] }}</p>
                            <ul class="space-y-3 mb-8">
                                @foreach ($service['features'] as $feature)
                                    <li class="flex items-center gap-3">
                                        @svg('lucide-check-circle', 'w-5 h-5 text-green-500 flex-shrink-0')
                                        <span class="text-gray-700 dark:text-gray-300">{{ $feature }}</span>
                                    </li>
                                @endforeach
                            </ul>
                            <a href="/booking" class="inline-flex items-center gap-2 bg-medical-blue text-white px-6 py-3 rounded-lg font-semibold hover:bg-medical-blue-dark transition-colors">
                                {{ __('bookNowShort') }}
                                @svg('lucide-arrow-left', 'w-[18px] h-[18px]')
                            </a>
                        </div>
                        <div class="{{ $index % 2 === 1 ? 'lg:order-1' : '' }}">
                            <div class="relative aspect-square rounded-3xl overflow-hidden shadow-2xl">
                                <img src="{{ $service['image'] }}" alt="{{ $service['title'] }}"
                                     class="w-full h-full object-cover transition-transform duration-500 hover:scale-110">
                                <div class="absolute inset-0 bg-gradient-to-br {{ $service['color'] }} opacity-20"></div>
                                <div class="absolute top-6 right-6 bg-white/90 backdrop-blur-sm p-4 rounded-2xl shadow-lg">
                                    @svg('lucide-' . $service['icon'], 'w-8 h-8 text-medical-blue')
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- CTA Section --}}
    <section class="py-16 bg-gray-50 dark:bg-gray-900">
        <div class="container-custom text-center">
            @svg('lucide-heart', 'w-12 h-12 mx-auto text-pink-500 mb-6')
            <h2 class="text-3xl font-bold text-gray-800 dark:text-white mb-4">{{ __('servicesCtaHeading') }}</h2>
            <p class="text-gray-600 dark:text-gray-400 text-lg mb-8 max-w-2xl mx-auto">
                {{ __('servicesCtaDesc') }}
            </p>
            <div class="flex flex-wrap justify-center gap-4">
                <a href="/booking" class="bg-medical-blue text-white px-8 py-4 rounded-lg font-semibold hover:bg-medical-blue-dark transition-colors">
                    {{ __('bookAppointmentBtn') }}
                </a>
                <a href="https://wa.me/{{ config('clinic.contact.whatsapp') }}" class="bg-green-500 text-white px-8 py-4 rounded-lg font-semibold hover:bg-green-600 transition-colors">
                    {{ __('contactViaWhatsAppBtn') }}
                </a>
            </div>
        </div>
    </section>
@endsection
