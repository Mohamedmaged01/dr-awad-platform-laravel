@extends('layouts.public')

@section('title', __('bookingPageTitle') . ' | ' . __('heroTitle'))

@section('content')
{{-- Mirrors booking/page.tsx: 3-step wizard, 2s simulated submit, success screen. --}}
<div x-data="{
        step: 1,
        submitting: false,
        success: false,
        form: { branch: '', service: '', date: '', time: '', name: '', phone: '', email: '', notes: '' },
        branches: @js($branches),
        services: @js($services),
        get selectedBranch() { return this.branches.find(b => b.value === this.form.branch) },
        get selectedService() { return this.services.find(s => s.value === this.form.service) },
        submit() { this.submitting = true; setTimeout(() => { this.submitting = false; this.success = true }, 2000) },
        reset() {
            this.success = false; this.step = 1;
            this.form = { branch: '', service: '', date: '', time: '', name: '', phone: '', email: '', notes: '' };
        }
     }">

    {{-- ===== Success state (server-rendered after a real submit) ===== --}}
    @if (session('booking_success'))
    @php $summary = session('booking_summary', []); @endphp
    <section class="pt-36 pb-16 min-h-[80vh] flex items-center">
        <div class="container-custom">
            <x-ui.card class="max-w-2xl mx-auto text-center">
                <x-ui.card-content class="py-16">
                    <div class="w-24 h-24 mx-auto mb-6 rounded-full bg-green-100 flex items-center justify-center">
                        @svg('lucide-check-circle', 'w-14 h-14 text-green-500')
                    </div>
                    <h2 class="text-3xl font-bold text-gray-800 dark:text-white mb-4">{{ __('bookingReceivedTitle') }}</h2>
                    <p class="text-gray-600 dark:text-gray-400 text-lg mb-6">
                        {{ __('bookingReceivedDescLong') }}
                    </p>
                    @if (! empty($summary))
                    <div class="bg-gray-50 dark:bg-gray-800 rounded-xl p-6 mb-8 text-start">
                        <h3 class="font-bold text-gray-800 dark:text-white mb-4">{{ __('bookingDetails') }}</h3>
                        <div class="space-y-2 text-gray-600 dark:text-gray-400">
                            <p><strong>{{ __('nameLabel') }}:</strong> {{ $summary['name'] ?? '' }}</p>
                            <p><strong>{{ __('date') }}:</strong> {{ $summary['date'] ?? '' }}</p>
                            <p><strong>{{ __('time') }}:</strong> {{ $summary['time'] ?? '' }}</p>
                        </div>
                    </div>
                    @endif
                    <div class="flex flex-col sm:flex-row gap-4 justify-center">
                        <x-ui.button href="/booking" variant="primary">{{ __('bookAnother') }}</x-ui.button>
                        <x-ui.button href="/" variant="outline">{{ __('backToHome') }}</x-ui.button>
                    </div>
                </x-ui.card-content>
            </x-ui.card>
        </div>
    </section>
    @else

    {{-- ===== Wizard ===== --}}
    <div>
        {{-- Hero --}}
        <section class="pt-32 pb-8 gradient-medical">
            <div class="container-custom text-center text-white">
                <h1 class="text-4xl md:text-5xl font-bold mb-4">{{ __('bookingPageTitle') }}</h1>
                <p class="text-xl text-white/80">{{ __('bookingPageSubtitle') }}</p>
            </div>
        </section>

        {{-- Progress Steps --}}
        <section class="py-8 bg-white dark:bg-gray-800 shadow-sm sticky top-20 z-30">
            <div class="container-custom">
                <div class="flex justify-center items-center gap-4">
                    @foreach ([1 => __('stepServiceTime'), 2 => __('stepInfo'), 3 => __('stepConfirm')] as $s => $label)
                        <div class="flex items-center gap-2" :class="step >= {{ $s }} ? 'text-medical-blue' : 'text-gray-400'">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold"
                                 :class="step >= {{ $s }} ? 'bg-medical-blue text-white' : 'bg-gray-200 text-gray-500'">
                                <span x-show="step > {{ $s }}" x-cloak>@svg('lucide-check-circle', 'w-5 h-5')</span>
                                <span x-show="step <= {{ $s }}">{{ $s }}</span>
                            </div>
                            <span class="hidden sm:inline font-medium">{{ $label }}</span>
                        </div>
                        @if ($s < 3)
                            <div class="w-16 h-1 rounded" :class="step > {{ $s }} ? 'bg-medical-blue' : 'bg-gray-200'"></div>
                        @endif
                    @endforeach
                </div>
            </div>
        </section>

        {{-- Form --}}
        <section class="py-12">
            <div class="container-custom">
                <x-ui.card class="max-w-3xl mx-auto shadow-xl">
                    <x-ui.card-content class="p-8">
                        @if ($errors->any())
                            <div class="mb-6 p-3 rounded-lg bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300 text-sm">
                                {{ $errors->first() }}
                            </div>
                        @endif
                        <form method="POST" action="{{ route('booking.submit') }}" @submit="submitting = true">
                            @csrf
                            <input type="hidden" name="time" :value="form.time">
                            {{-- Step 1: Service & Time --}}
                            <div x-show="step === 1" class="space-y-6">
                                <h2 class="text-2xl font-bold text-gray-800 dark:text-white mb-6">{{ __('step1Heading') }}</h2>

                                <x-ui.select :label="__('branch')" name="branch" x-model="form.branch"
                                             :options="collect($branches)->map(fn ($b) => ['value' => $b['value'], 'label' => $b['label']])->all()"
                                             :placeholder="__('selectBranchPlaceholder')" required />

                                <x-ui.select :label="__('serviceType')" name="service" x-model="form.service"
                                             :options="collect($services)->map(fn ($s) => ['value' => $s['value'], 'label' => $s['label'] . ' - ' . $s['price'] . ' ' . __('egp')])->all()"
                                             :placeholder="__('selectServicePlaceholder')" required />

                                <x-ui.input :label="__('date')" name="date" type="date" x-model="form.date"
                                            min="{{ now()->toDateString() }}" required />

                                <div x-show="form.date" x-cloak>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">{{ __('chooseTime') }}</label>
                                    <div class="grid grid-cols-4 sm:grid-cols-6 gap-2">
                                        @foreach ($timeSlots as $slot)
                                            <button type="button" @click="form.time = '{{ $slot }}'"
                                                    :class="form.time === '{{ $slot }}'
                                                        ? 'bg-medical-blue text-white'
                                                        : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-medical-blue/10'"
                                                    class="py-2 px-3 rounded-lg text-sm font-medium transition-colors">
                                                {{ $slot }}
                                            </button>
                                        @endforeach
                                    </div>
                                </div>

                                <div class="flex justify-end pt-6">
                                    <x-ui.button type="button" variant="primary" size="lg" x-on:click="step = 2"
                                                 ::disabled="!form.branch || !form.service || !form.date || !form.time">
                                        {{ __('next') }}
                                        <x-slot:rightIcon>@svg('lucide-arrow-left', 'w-[18px] h-[18px]')</x-slot:rightIcon>
                                    </x-ui.button>
                                </div>
                            </div>

                            {{-- Step 2: Personal Info --}}
                            <div x-show="step === 2" x-cloak class="space-y-6">
                                <h2 class="text-2xl font-bold text-gray-800 dark:text-white mb-6">{{ __('step2Heading') }}</h2>

                                <x-ui.input :label="__('fullName')" name="name" x-model="form.name" :placeholder="__('enterFullName')" required>
                                    <x-slot:leftIcon>@svg('lucide-user', 'w-[18px] h-[18px]')</x-slot:leftIcon>
                                </x-ui.input>

                                <x-ui.input :label="__('phone')" name="phone" type="tel" x-model="form.phone" placeholder="01xxxxxxxxx" required>
                                    <x-slot:leftIcon>@svg('lucide-phone', 'w-[18px] h-[18px]')</x-slot:leftIcon>
                                </x-ui.input>

                                <x-ui.input :label="__('emailOptional')" name="email" type="email" x-model="form.email" placeholder="example@email.com">
                                    <x-slot:leftIcon>@svg('lucide-mail', 'w-[18px] h-[18px]')</x-slot:leftIcon>
                                </x-ui.input>

                                <x-ui.textarea :label="__('notesOptional')" name="notes" x-model="form.notes" rows="3"
                                               :placeholder="__('notesPlaceholderLong')" />

                                <div class="flex justify-between pt-6">
                                    <x-ui.button type="button" variant="outline" size="lg" x-on:click="step = 1">
                                        <x-slot:leftIcon>@svg('lucide-arrow-right', 'w-[18px] h-[18px]')</x-slot:leftIcon>
                                        {{ __('prev') }}
                                    </x-ui.button>
                                    <x-ui.button type="button" variant="primary" size="lg" x-on:click="step = 3"
                                                 ::disabled="!form.name || !form.phone">
                                        {{ __('reviewBooking') }}
                                        <x-slot:rightIcon>@svg('lucide-arrow-left', 'w-[18px] h-[18px]')</x-slot:rightIcon>
                                    </x-ui.button>
                                </div>
                            </div>

                            {{-- Step 3: Confirmation --}}
                            <div x-show="step === 3" x-cloak class="space-y-6">
                                <h2 class="text-2xl font-bold text-gray-800 dark:text-white mb-6">{{ __('confirmBooking') }}</h2>

                                <div class="bg-gray-50 dark:bg-gray-800 rounded-xl p-6 space-y-4">
                                    <div class="flex items-center gap-3 pb-4 border-b border-gray-200 dark:border-gray-700">
                                        @svg('lucide-map-pin', 'w-5 h-5 text-medical-blue')
                                        <div>
                                            <p class="font-medium text-gray-800 dark:text-white" x-text="selectedBranch?.label"></p>
                                            <p class="text-sm text-gray-500" x-text="selectedBranch?.address"></p>
                                        </div>
                                    </div>

                                    <div class="flex items-center gap-3 pb-4 border-b border-gray-200 dark:border-gray-700">
                                        @svg('lucide-calendar', 'w-5 h-5 text-medical-blue')
                                        <div>
                                            <p class="font-medium text-gray-800 dark:text-white" x-text="form.date"></p>
                                            <p class="text-sm text-gray-500">{{ __('selectedDate') }}</p>
                                        </div>
                                    </div>

                                    <div class="flex items-center gap-3 pb-4 border-b border-gray-200 dark:border-gray-700">
                                        @svg('lucide-clock', 'w-5 h-5 text-medical-blue')
                                        <div>
                                            <p class="font-medium text-gray-800 dark:text-white" x-text="form.time"></p>
                                            <p class="text-sm text-gray-500">{{ __('selectedTime') }}</p>
                                        </div>
                                    </div>

                                    <div class="flex items-center gap-3 pb-4 border-b border-gray-200 dark:border-gray-700">
                                        @svg('lucide-user', 'w-5 h-5 text-medical-blue')
                                        <div>
                                            <p class="font-medium text-gray-800 dark:text-white" x-text="form.name"></p>
                                            <p class="text-sm text-gray-500" x-text="form.phone"></p>
                                        </div>
                                    </div>

                                    <div class="flex items-center justify-between pt-2">
                                        <div>
                                            <p class="font-medium text-gray-800 dark:text-white" x-text="selectedService?.label"></p>
                                            <p class="text-sm text-gray-500">{{ __('requestedService') }}</p>
                                        </div>
                                        <div class="text-2xl font-bold text-medical-blue">
                                            <span x-text="selectedService?.price"></span> {{ __('egp') }}
                                        </div>
                                    </div>
                                </div>

                                <p class="text-sm text-gray-500 text-center">
                                    {{ __('confirmNote') }}
                                </p>

                                <div class="flex justify-between pt-6">
                                    <x-ui.button type="button" variant="outline" size="lg" x-on:click="step = 2">
                                        <x-slot:leftIcon>@svg('lucide-arrow-right', 'w-[18px] h-[18px]')</x-slot:leftIcon>
                                        {{ __('prev') }}
                                    </x-ui.button>

                                    <button type="submit" :disabled="submitting"
                                            class="inline-flex items-center justify-center font-semibold rounded-lg transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed px-7 py-3.5 text-lg gap-2.5 bg-light-gold text-white hover:bg-light-gold-light focus:ring-light-gold shadow-md hover:shadow-lg">
                                        <span x-show="submitting" x-cloak>
                                            <svg class="animate-spin h-5 w-5" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none" />
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" />
                                            </svg>
                                        </span>
                                        <span x-show="!submitting" class="flex-shrink-0">@svg('lucide-check-circle', 'w-[18px] h-[18px]')</span>
                                        {{ __('confirmBooking') }}
                                    </button>
                                </div>
                            </div>
                        </form>
                    </x-ui.card-content>
                </x-ui.card>
            </div>
        </section>
    </div>
    @endif
</div>
@endsection
