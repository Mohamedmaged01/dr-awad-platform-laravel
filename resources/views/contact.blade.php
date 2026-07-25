@extends('layouts.public')

@section('title', __('contactTitle') . ' | ' . __('heroTitle'))

@section('content')
    {{-- Hero --}}
    <section class="pt-32 pb-16 gradient-medical">
        <div class="container-custom text-center text-white">
            <h1 class="text-4xl md:text-5xl font-bold mb-4">{{ __('contactTitle') }}</h1>
            <p class="text-xl text-white/80 max-w-2xl mx-auto">
                {{ __('contactSubtitle') }}
            </p>
        </div>
    </section>

    {{-- Contact Info & Form --}}
    <section class="py-16">
        <div class="container-custom">
            <div class="grid lg:grid-cols-2 gap-12">
                {{-- Contact Form (simulates a 2s submit, as in the source) --}}
                <x-ui.card class="shadow-xl"
                           x-data="{ submitting: false, success: false, submit() { this.submitting = true; setTimeout(() => { this.submitting = false; this.success = true }, 2000) } }">
                    <x-ui.card-content class="p-8">
                        <div x-show="success" x-cloak class="text-center py-12">
                            <div class="w-20 h-20 mx-auto mb-6 rounded-full bg-green-100 flex items-center justify-center">
                                @svg('lucide-check-circle', 'w-12 h-12 text-green-500')
                            </div>
                            <h3 class="text-2xl font-bold text-gray-800 dark:text-white mb-4">{{ __('messageSent') }}</h3>
                            <p class="text-gray-600 dark:text-gray-400 mb-6">
                                {{ __('contactSuccessDesc') }}
                            </p>
                            <x-ui.button variant="primary" x-on:click="success = false">{{ __('sendAnother') }}</x-ui.button>
                        </div>

                        <div x-show="!success">
                            <h2 class="text-2xl font-bold text-gray-800 dark:text-white mb-6">{{ __('sendUsMessage') }}</h2>
                            <form class="space-y-5" @submit.prevent="submit()">
                                <x-ui.input :label="__('fullName')" name="name" :placeholder="__('enterFullName')" required />
                                <div class="grid md:grid-cols-2 gap-5">
                                    <x-ui.input :label="__('phone')" name="phone" type="tel" placeholder="01xxxxxxxxx" required />
                                    <x-ui.input :label="__('email')" name="email" type="email" placeholder="example@email.com" />
                                </div>
                                <x-ui.input :label="__('subject')" name="subject" :placeholder="__('subjectPlaceholder')" required />
                                <x-ui.textarea :label="__('yourMessage')" name="message" rows="5" :placeholder="__('messagePlaceholder')" required />

                                <button type="submit" :disabled="submitting"
                                        class="inline-flex items-center justify-center font-semibold rounded-lg transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed px-7 py-3.5 text-lg gap-2.5 bg-medical-blue text-white hover:bg-medical-blue-dark focus:ring-medical-blue shadow-md hover:shadow-lg w-full">
                                    <span x-show="submitting" x-cloak>
                                        <svg class="animate-spin h-5 w-5" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none" />
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" />
                                        </svg>
                                    </span>
                                    <span x-show="!submitting" class="flex-shrink-0">@svg('lucide-send', 'w-[18px] h-[18px]')</span>
                                    {{ __('sendMessageBtn') }}
                                </button>
                            </form>
                        </div>
                    </x-ui.card-content>
                </x-ui.card>

                {{-- Quick Contact --}}
                <div class="space-y-6">
                    <h2 class="text-2xl font-bold text-gray-800 dark:text-white mb-6">{{ __('quickContact') }}</h2>

                    <div class="grid gap-4">
                        <a href="tel:{{ config('clinic.contact.phone_tel') }}"
                           class="flex items-center gap-4 p-5 bg-white dark:bg-gray-800 rounded-xl shadow-md hover:shadow-lg transition-shadow">
                            <div class="w-14 h-14 rounded-full bg-medical-blue/10 flex items-center justify-center flex-shrink-0">
                                @svg('lucide-phone', 'w-6 h-6 text-medical-blue')
                            </div>
                            <div>
                                <p class="font-semibold text-gray-800 dark:text-white">{{ __('callNow') }}</p>
                                <p class="text-medical-blue">{{ config('clinic.contact.phone_display') }}</p>
                            </div>
                        </a>

                        <a href="https://wa.me/{{ config('clinic.contact.whatsapp') }}" target="_blank" rel="noopener noreferrer"
                           class="flex items-center gap-4 p-5 bg-white dark:bg-gray-800 rounded-xl shadow-md hover:shadow-lg transition-shadow">
                            <div class="w-14 h-14 rounded-full bg-green-100 flex items-center justify-center flex-shrink-0">
                                @svg('lucide-message-circle', 'w-6 h-6 text-green-600')
                            </div>
                            <div>
                                <p class="font-semibold text-gray-800 dark:text-white">{{ __('whatsapp') }}</p>
                                <p class="text-green-600">{{ config('clinic.contact.phone_display') }}</p>
                            </div>
                        </a>

                        <a href="mailto:{{ config('clinic.contact.email') }}"
                           class="flex items-center gap-4 p-5 bg-white dark:bg-gray-800 rounded-xl shadow-md hover:shadow-lg transition-shadow">
                            <div class="w-14 h-14 rounded-full bg-light-gold/10 flex items-center justify-center flex-shrink-0">
                                @svg('lucide-mail', 'w-6 h-6 text-light-gold')
                            </div>
                            <div>
                                <p class="font-semibold text-gray-800 dark:text-white">{{ __('email') }}</p>
                                <p class="text-light-gold">{{ config('clinic.contact.email') }}</p>
                            </div>
                        </a>
                    </div>

                    <div class="p-5 bg-gray-50 dark:bg-gray-800 rounded-xl">
                        <div class="flex items-center gap-3 mb-3">
                            @svg('lucide-clock', 'w-5 h-5 text-medical-blue')
                            <h3 class="font-semibold text-gray-800 dark:text-white">{{ __('workingHoursLabel') }}</h3>
                        </div>
                        <div class="space-y-2 text-gray-600 dark:text-gray-400">
                            <p>{{ __('hoursDailyLong') }}</p>
                            <p>{{ __('forBookingInquiries') }} {{ config('clinic.contact.phone_display') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Branches --}}
    <section class="py-16 bg-gray-50 dark:bg-gray-900">
        <div class="container-custom">
            <h2 class="text-3xl font-bold text-gray-800 dark:text-white text-center mb-12">{{ __('ourBranches') }}</h2>
            <div class="grid md:grid-cols-2 gap-8">
                @foreach ($branches as $branch)
                    <x-ui.card class="overflow-hidden">
                        <x-ui.card-content class="p-6">
                            <h3 class="text-xl font-bold text-gray-800 dark:text-white mb-4">{{ $branch['name'] }}</h3>
                            <div class="space-y-3 text-gray-600 dark:text-gray-400">
                                <div class="flex items-start gap-3">
                                    @svg('lucide-map-pin', 'w-[18px] h-[18px] text-medical-blue flex-shrink-0 mt-1')
                                    <span>{{ $branch['address'] }}</span>
                                </div>
                                <div class="flex items-center gap-3">
                                    @svg('lucide-phone', 'w-[18px] h-[18px] text-medical-blue flex-shrink-0')
                                    <a href="tel:{{ $branch['phone'] }}" class="hover:text-medical-blue">{{ $branch['phone'] }}</a>
                                </div>
                                <div class="flex items-center gap-3">
                                    @svg('lucide-mail', 'w-[18px] h-[18px] text-medical-blue flex-shrink-0')
                                    <a href="mailto:{{ $branch['email'] }}" class="hover:text-medical-blue">{{ $branch['email'] }}</a>
                                </div>
                                <div class="flex items-center gap-3">
                                    @svg('lucide-clock', 'w-[18px] h-[18px] text-medical-blue flex-shrink-0')
                                    <span>{{ $branch['hours'] }}</span>
                                </div>
                            </div>
                            <div class="mt-6 flex gap-3">
                                <a href="{{ $branch['mapUrl'] }}" target="_blank" rel="noopener noreferrer"
                                   class="flex-1 text-center py-2 bg-medical-blue text-white rounded-lg hover:bg-medical-blue-dark transition-colors">
                                    {{ __('mapBtn') }}
                                </a>
                                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $branch['whatsapp']) }}" target="_blank" rel="noopener noreferrer"
                                   class="flex-1 text-center py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-colors">
                                    {{ __('whatsapp') }}
                                </a>
                            </div>
                        </x-ui.card-content>
                    </x-ui.card>
                @endforeach
            </div>
        </div>
    </section>
@endsection
