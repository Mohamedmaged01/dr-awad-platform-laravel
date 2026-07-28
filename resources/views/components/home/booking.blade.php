@props(['branches' => [], 'services' => []])
@php $contact = config('clinic.contact'); @endphp
{{-- Mirrors BookingSection.tsx: submit simulates a 2s request, then swaps to a success card. --}}
<section id="booking" class="section-padding bg-gray-50 dark:bg-gray-900" x-data="{ submitting: false }">
    <div class="container-custom">
        @if (session('booking_success'))
        {{-- Success state (server-rendered after a real submit) --}}
        <div>
            <x-ui.card class="max-w-2xl mx-auto text-center">
                <x-ui.card-content class="py-16">
                    <div class="w-20 h-20 mx-auto mb-6 rounded-full bg-green-100 flex items-center justify-center">
                        @svg('lucide-check-circle', 'w-12 h-12 text-green-500')
                    </div>
                    <h3 class="text-2xl font-bold text-gray-800 dark:text-white mb-4">{{ __('bookingReceivedTitle') }}</h3>
                    <p class="text-gray-600 dark:text-gray-400 mb-8">{{ __('bookingReceivedDesc') }}</p>
                    <x-ui.button href="/#booking" variant="primary">{{ __('bookAnother') }}</x-ui.button>
                </x-ui.card-content>
            </x-ui.card>
        </div>
        @else
        <div class="grid lg:grid-cols-2 gap-12">
            {{-- Info Section --}}
            <div>
                <span class="inline-block text-medical-blue dark:text-light-gold font-semibold mb-4">{{ __('bookingEyebrow') }}</span>
                <h2 class="heading-primary mb-6">{{ __('bookingHelpTitle') }}</h2>
                <p class="text-gray-600 dark:text-gray-400 mb-8 leading-relaxed">
                    {{ __('bookingHelpDesc') }}
                </p>

                <div class="space-y-4">
                    <div class="flex items-center gap-4 p-4 bg-white dark:bg-gray-800 rounded-xl shadow-sm">
                        <div class="w-12 h-12 rounded-full bg-medical-blue/10 flex items-center justify-center flex-shrink-0">
                            @svg('lucide-phone', 'w-6 h-6 text-medical-blue')
                        </div>
                        <div>
                            <p class="font-semibold text-gray-800 dark:text-white">{{ __('callNow') }}</p>
                            <a href="tel:{{ $contact['phone_tel'] }}" class="text-medical-blue">{{ $contact['phone_display'] }}</a>
                        </div>
                    </div>

                    <div class="flex items-center gap-4 p-4 bg-white dark:bg-gray-800 rounded-xl shadow-sm">
                        <div class="w-12 h-12 rounded-full bg-green-100 flex items-center justify-center flex-shrink-0">
                            @svg('lucide-message-square', 'w-6 h-6 text-green-600')
                        </div>
                        <div>
                            <p class="font-semibold text-gray-800 dark:text-white">{{ __('whatsapp') }}</p>
                            <a href="https://wa.me/{{ $contact['whatsapp'] }}" class="text-green-600">{{ $contact['phone_display'] }}</a>
                        </div>
                    </div>

                    <div class="flex items-center gap-4 p-4 bg-white dark:bg-gray-800 rounded-xl shadow-sm">
                        <div class="w-12 h-12 rounded-full bg-light-gold/10 flex items-center justify-center flex-shrink-0">
                            @svg('lucide-clock', 'w-6 h-6 text-light-gold')
                        </div>
                        <div>
                            <p class="font-semibold text-gray-800 dark:text-white">{{ __('workingHoursLabel') }}</p>
                            <p class="text-gray-600 dark:text-gray-400">{{ __('hoursDaily') }}</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-4 p-4 bg-white dark:bg-gray-800 rounded-xl shadow-sm">
                        <div class="w-12 h-12 rounded-full bg-pink-100 flex items-center justify-center flex-shrink-0">
                            @svg('lucide-map-pin', 'w-6 h-6 text-pink-600')
                        </div>
                        <div>
                            <p class="font-semibold text-gray-800 dark:text-white">{{ __('ourBranches') }}</p>
                            <div class="text-gray-600 dark:text-gray-400 text-sm space-y-1">
                                <p>{{ __('bookingBranch1') }}</p>
                                <p>{{ __('bookingBranch2') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Booking Form --}}
            <x-ui.card class="shadow-xl">
                <x-ui.card-header>
                    <h3 class="text-xl font-bold text-gray-800 dark:text-white">{{ __('quickBookingForm') }}</h3>
                </x-ui.card-header>
                <x-ui.card-content>
                    @if ($errors->any())
                        <div class="mb-5 p-3 rounded-lg bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300 text-sm">
                            {{ $errors->first() }}
                        </div>
                    @endif
                    <form method="POST" action="{{ route('booking.submit') }}" class="space-y-5" @submit="submitting = true">
                        @csrf
                        <x-ui.input :label="__('fullName')" name="name" :placeholder="__('enterFullName')" required>
                            <x-slot:leftIcon>@svg('lucide-user', 'w-[18px] h-[18px]')</x-slot:leftIcon>
                        </x-ui.input>

                        <x-ui.input :label="__('phone')" name="phone" type="tel" placeholder="01xxxxxxxxx" required>
                            <x-slot:leftIcon>@svg('lucide-phone', 'w-[18px] h-[18px]')</x-slot:leftIcon>
                        </x-ui.input>

                        <x-ui.input :label="__('email')" name="email" type="email" placeholder="example@email.com">
                            <x-slot:leftIcon>@svg('lucide-mail', 'w-[18px] h-[18px]')</x-slot:leftIcon>
                        </x-ui.input>

                        <x-ui.select :label="__('branch')" name="branch" :options="$branches" :placeholder="__('selectBranchPlaceholder')" required />
                        <x-ui.select :label="__('serviceType')" name="service" :options="$services" :placeholder="__('selectServicePlaceholder')" required />

                        <div class="grid grid-cols-2 gap-4">
                            <x-ui.input :label="__('preferredDate')" name="date" type="date" required />
                            <x-ui.input :label="__('preferredTime')" name="time" type="time" />
                        </div>

                        <x-ui.textarea :label="__('notes')" name="notes" rows="3" :placeholder="__('notesPlaceholder')" />

                        {{-- Loading spinner swaps in while "submitting", as in the source. --}}
                        <button type="submit" :disabled="submitting"
                                class="inline-flex items-center justify-center font-semibold rounded-lg transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed px-7 py-3.5 text-lg gap-2.5 bg-light-gold text-white hover:bg-light-gold-light focus:ring-light-gold shadow-md hover:shadow-lg w-full">
                            <span x-show="submitting" x-cloak>
                                <svg class="animate-spin h-5 w-5" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none" />
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" />
                                </svg>
                            </span>
                            <span x-show="!submitting" class="flex-shrink-0">@svg('lucide-calendar', 'w-5 h-5')</span>
                            {{ __('confirmBooking') }}
                        </button>

                        <p class="text-center text-sm text-gray-500 dark:text-gray-400">{{ __('bookingConfirmNote') }}</p>
                    </form>
                </x-ui.card-content>
            </x-ui.card>
        </div>
        @endif
    </div>
</section>
