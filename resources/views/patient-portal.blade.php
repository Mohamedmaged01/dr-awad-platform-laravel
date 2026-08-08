@extends('layouts.public')

@section('title', __('patientPortal') . ' | ' . __('heroTitle'))

@section('content')
{{-- Real patient auth: login/register when signed out, live dashboard when signed in. --}}
<div x-data="{ showLogin: {{ $errors->any() && ! $errors->has('portal') ? 'false' : 'true' }} }">

    @if (! $patient)
    {{-- ===== Login / Register ===== --}}
    <section class="pt-36 pb-16 min-h-[80vh] flex items-center gradient-soft">
        <div class="container-custom">
            <div class="max-w-md mx-auto">
                <x-ui.card class="shadow-xl">
                    <x-ui.card-content class="p-8">
                        <div class="text-center mb-8">
                            <div class="w-20 h-20 mx-auto mb-4 rounded-full gradient-medical flex items-center justify-center">
                                @svg('lucide-user', 'w-10 h-10 text-white')
                            </div>
                            <h1 class="text-2xl font-bold text-gray-800 dark:text-white mb-2">{{ __('patientPortal') }}</h1>
                            <p class="text-gray-500" x-text="showLogin ? @js(__('portalLoginSubtitle')) : @js(__('portalRegisterSubtitle'))"></p>
                        </div>

                        <div class="flex rounded-lg border border-gray-200 dark:border-gray-700 mb-6 overflow-hidden">
                            <button @click="showLogin = true" class="flex-1 py-2 text-sm font-medium"
                                    :class="showLogin && 'bg-medical-blue text-white'">{{ __('login') }}</button>
                            <button @click="showLogin = false" class="flex-1 py-2 text-sm font-medium"
                                    :class="!showLogin && 'bg-medical-blue text-white'">{{ __('newAccount') }}</button>
                        </div>

                        @if ($errors->has('portal'))
                            <div class="mb-5 p-3 rounded-lg bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300 text-sm">
                                {{ $errors->first('portal') }}
                            </div>
                        @endif

                        {{-- Login --}}
                        <form x-show="showLogin" method="POST" action="{{ route('patient.login') }}" class="space-y-5">
                            @csrf
                            <x-ui.input :label="__('emailOrPhone')" name="email" type="email" :value="old('email')" placeholder="example@email.com" required>
                                <x-slot:leftIcon>@svg('lucide-mail', 'w-[18px] h-[18px]')</x-slot:leftIcon>
                            </x-ui.input>
                            <x-ui.input :label="__('password')" name="password" type="password" placeholder="••••••••" required>
                                <x-slot:leftIcon>@svg('lucide-lock', 'w-[18px] h-[18px]')</x-slot:leftIcon>
                            </x-ui.input>
                            <div class="flex items-center justify-between">
                                <label class="flex items-center gap-2 text-sm">
                                    <input type="checkbox" class="rounded">
                                    <span class="text-gray-600 dark:text-gray-400">{{ __('rememberMe') }}</span>
                                </label>
                                <a href="#" class="text-sm text-medical-blue hover:underline">{{ __('forgotPassword') }}</a>
                            </div>
                            <x-ui.button type="submit" variant="primary" size="lg" class="w-full">
                                <x-slot:leftIcon>@svg('lucide-log-in', 'w-[18px] h-[18px]')</x-slot:leftIcon>
                                {{ __('login') }}
                            </x-ui.button>
                        </form>

                        {{-- Register --}}
                        <form x-show="!showLogin" x-cloak method="POST" action="{{ route('patient.register') }}" class="space-y-5">
                            @csrf
                            <x-ui.input :label="__('fullName')" name="name" :value="old('name')" :error="$errors->first('name')" :placeholder="__('enterYourName')" required>
                                <x-slot:leftIcon>@svg('lucide-user', 'w-[18px] h-[18px]')</x-slot:leftIcon>
                            </x-ui.input>
                            <x-ui.input :label="__('phone')" name="phone" type="tel" :value="old('phone')" :error="$errors->first('phone')" placeholder="01xxxxxxxxx" required>
                                <x-slot:leftIcon>@svg('lucide-phone', 'w-[18px] h-[18px]')</x-slot:leftIcon>
                            </x-ui.input>
                            <x-ui.input :label="__('email')" name="email" type="email" :value="old('email')" :error="$errors->first('email')" placeholder="example@email.com" required>
                                <x-slot:leftIcon>@svg('lucide-mail', 'w-[18px] h-[18px]')</x-slot:leftIcon>
                            </x-ui.input>
                            <x-ui.input :label="__('password')" name="password" type="password" :error="$errors->first('password')" placeholder="••••••••" required>
                                <x-slot:leftIcon>@svg('lucide-lock', 'w-[18px] h-[18px]')</x-slot:leftIcon>
                            </x-ui.input>
                            <x-ui.input :label="__('fileNumberOptional')" name="file_number" :value="old('file_number')" placeholder="P2024XXXXX">
                                <x-slot:leftIcon>@svg('lucide-file-text', 'w-[18px] h-[18px]')</x-slot:leftIcon>
                            </x-ui.input>
                            <x-ui.button type="submit" variant="primary" size="lg" class="w-full">{{ __('createAccount') }}</x-ui.button>
                        </form>

                        <p class="text-center text-sm text-gray-500 mt-6">
                            {{ __('agreeToTerms') }}
                            <a href="/terms" class="text-medical-blue hover:underline">{{ __('termsConditions') }}</a>
                            {{ __('andWord') }}
                            <a href="/privacy" class="text-medical-blue hover:underline">{{ __('privacyPolicy') }}</a>
                        </p>
                    </x-ui.card-content>
                </x-ui.card>
            </div>
        </div>
    </section>

    @else
    {{-- ===== Live Dashboard ===== --}}
    <div class="bg-gray-50 dark:bg-gray-900">
        <section class="pt-36 pb-16">
            <div class="container-custom">
                {{-- Welcome --}}
                <div class="bg-gradient-to-r from-medical-blue to-medical-blue-dark rounded-2xl p-8 text-white mb-8">
                    <div class="flex items-center gap-4">
                        <div class="w-16 h-16 rounded-full bg-white/20 flex items-center justify-center text-2xl font-bold">{{ mb_substr($patient->name, 0, 1) }}</div>
                        <div>
                            <h1 class="text-2xl font-bold">{{ __('welcomeBack') }}، {{ $patient->first_name_ar }}</h1>
                            <p class="text-white/80">{{ __('fileNumberColon') }} {{ $patient->file_number }}</p>
                        </div>
                    </div>
                </div>

                {{-- Quick Actions --}}
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
                    @foreach ([
                        ['label' => __('booking'), 'icon' => 'calendar', 'href' => '/booking', 'color' => 'bg-blue-500'],
                        ['label' => __('medicalRecords'), 'icon' => 'file-text', 'href' => '#records', 'color' => 'bg-green-500'],
                        ['label' => __('pregnancyTracking'), 'icon' => 'baby', 'href' => '#pregnancy', 'color' => 'bg-pink-500'],
                        ['label' => __('messageToDoctor'), 'icon' => 'message-square', 'href' => '#messages', 'color' => 'bg-purple-500'],
                    ] as $action)
                        <a href="{{ $action['href'] }}"
                           class="bg-white dark:bg-gray-800 rounded-xl p-4 shadow-md hover:shadow-lg transition-shadow flex flex-col items-center gap-3">
                            <div class="w-12 h-12 rounded-xl {{ $action['color'] }} flex items-center justify-center">
                                @svg('lucide-' . $action['icon'], 'w-6 h-6 text-white')
                            </div>
                            <span class="font-medium text-gray-800 dark:text-white">{{ $action['label'] }}</span>
                        </a>
                    @endforeach
                </div>

                <div class="grid lg:grid-cols-3 gap-8">
                    {{-- Main Content --}}
                    <div class="lg:col-span-2 space-y-6">
                        {{-- Upcoming Appointments --}}
                        <x-ui.card>
                            <x-ui.card-header class="flex justify-between items-center">
                                <h2 class="text-lg font-bold text-gray-800 dark:text-white">{{ __('upcomingAppointments') }}</h2>
                                <a href="/booking" class="text-medical-blue text-sm hover:underline">{{ __('newBooking') }}</a>
                            </x-ui.card-header>
                            <x-ui.card-content>
                                <div class="space-y-4">
                                    @forelse ($appointments as $appointment)
                                        <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-xl">
                                            <div class="flex items-center gap-4">
                                                <div class="w-12 h-12 rounded-xl bg-medical-blue/10 flex items-center justify-center">
                                                    @svg('lucide-calendar', 'w-6 h-6 text-medical-blue')
                                                </div>
                                                <div>
                                                    <p class="font-medium text-gray-800 dark:text-white">{{ $appointment['service'] }}</p>
                                                    <p class="text-sm text-gray-500">{{ $appointment['branch'] }}</p>
                                                </div>
                                            </div>
                                            <div class="text-left">
                                                <p class="font-medium text-gray-800 dark:text-white">{{ $appointment['date'] }}</p>
                                                <p class="text-sm text-gray-500">{{ $appointment['time'] }}</p>
                                            </div>
                                        </div>
                                    @empty
                                        <p class="text-center text-gray-500 py-6">{{ __('noAppointments') }}</p>
                                    @endforelse
                                </div>
                            </x-ui.card-content>
                        </x-ui.card>

                        {{-- Pregnancy Tracking --}}
                        <x-ui.card>
                            <x-ui.card-header class="flex justify-between items-center">
                                <h2 class="text-lg font-bold text-gray-800 dark:text-white">{{ __('pregnancyTracking') }}</h2>
                                <span class="px-3 py-1 bg-pink-100 text-pink-700 rounded-full text-sm">{{ __('week24Badge') }}</span>
                            </x-ui.card-header>
                            <x-ui.card-content>
                                <div class="text-center mb-6">
                                    <div class="w-32 h-32 mx-auto mb-4 rounded-full bg-pink-100 flex items-center justify-center">
                                        @svg('lucide-baby', 'w-16 h-16 text-pink-500')
                                    </div>
                                    <h3 class="text-xl font-bold text-gray-800 dark:text-white">{{ __('babySize') }}</h3>
                                    <p class="text-gray-500">{{ __('approxWeight') }}</p>
                                </div>
                                <div class="grid grid-cols-3 gap-4 mb-6">
                                    <div class="text-center p-3 bg-gray-50 dark:bg-gray-700 rounded-xl">
                                        <p class="text-2xl font-bold text-medical-blue">24</p>
                                        <p class="text-xs text-gray-500">{{ __('weekUnit') }}</p>
                                    </div>
                                    <div class="text-center p-3 bg-gray-50 dark:bg-gray-700 rounded-xl">
                                        <p class="text-2xl font-bold text-pink-500">112</p>
                                        <p class="text-xs text-gray-500">{{ __('daysRemaining') }}</p>
                                    </div>
                                    <div class="text-center p-3 bg-gray-50 dark:bg-gray-700 rounded-xl">
                                        <p class="text-2xl font-bold text-green-500">60%</p>
                                        <p class="text-xs text-gray-500">{{ __('completedLabel') }}</p>
                                    </div>
                                </div>
                                <div class="h-2 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                                    <div class="h-full w-3/5 bg-gradient-to-r from-pink-500 to-medical-blue rounded-full"></div>
                                </div>
                            </x-ui.card-content>
                        </x-ui.card>

                        {{-- Medical Records --}}
                        <x-ui.card>
                            <x-ui.card-header>
                                <h2 class="text-lg font-bold text-gray-800 dark:text-white">{{ __('medicalRecords') }}</h2>
                            </x-ui.card-header>
                            <x-ui.card-content>
                                <div class="space-y-3">
                                    @forelse ($records as $record)
                                        <div class="flex items-center justify-between p-3 border border-gray-200 dark:border-gray-700 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-800 cursor-pointer">
                                            <div class="flex items-center gap-3">
                                                @svg('lucide-file-text', 'w-5 h-5 text-medical-blue')
                                                <div>
                                                    <p class="font-medium text-gray-800 dark:text-white">{{ $record['name'] }}</p>
                                                    <p class="text-xs text-gray-500">{{ $record['type'] }} - {{ $record['date'] }}</p>
                                                </div>
                                            </div>
                                            <button class="text-medical-blue text-sm hover:underline">{{ __('view') }}</button>
                                        </div>
                                    @empty
                                        <p class="text-center text-gray-500 py-6">{{ __('noRecords') }}</p>
                                    @endforelse
                                </div>
                            </x-ui.card-content>
                        </x-ui.card>
                    </div>

                    {{-- Sidebar --}}
                    <div class="space-y-6">
                        {{-- Profile Card --}}
                        <x-ui.card>
                            <x-ui.card-content class="p-6">
                                <div class="text-center">
                                    <div class="w-20 h-20 mx-auto mb-4 rounded-full bg-medical-blue/10 flex items-center justify-center text-3xl font-bold text-medical-blue">{{ mb_substr($patient->name, 0, 1) }}</div>
                                    <h3 class="font-bold text-gray-800 dark:text-white">{{ $patient->name }}</h3>
                                    <p class="text-sm text-gray-500 mb-4">{{ __('fileNumberColon') }} {{ $patient->file_number }}</p>
                                    <div class="text-start space-y-2 text-sm">
                                        <p class="text-gray-600 dark:text-gray-400">📱 {{ $patient->phone }}</p>
                                        @if ($patient->email)<p class="text-gray-600 dark:text-gray-400">✉️ {{ $patient->email }}</p>@endif
                                    </div>
                                </div>
                            </x-ui.card-content>
                        </x-ui.card>

                        {{-- Notifications --}}
                        <x-ui.card>
                            <x-ui.card-header>
                                <h3 class="font-bold text-gray-800 dark:text-white flex items-center gap-2">
                                    @svg('lucide-bell', 'w-[18px] h-[18px]')
                                    {{ __('notifications') }}
                                </h3>
                            </x-ui.card-header>
                            <x-ui.card-content>
                                <div class="space-y-3">
                                    <div class="p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                                        <p class="text-sm text-gray-700 dark:text-gray-300">{{ __('notif1') }}</p>
                                        <p class="text-xs text-gray-500 mt-1">{{ __('notif1Time') }}</p>
                                    </div>
                                    <div class="p-3 bg-green-50 dark:bg-green-900/20 rounded-lg">
                                        <p class="text-sm text-gray-700 dark:text-gray-300">{{ __('notif2') }}</p>
                                        <p class="text-xs text-gray-500 mt-1">{{ __('notif2Time') }}</p>
                                    </div>
                                </div>
                            </x-ui.card-content>
                        </x-ui.card>

                        {{-- Quick Links --}}
                        <x-ui.card>
                            <x-ui.card-content class="p-4">
                                <div class="space-y-2">
                                    <a href="#" class="flex items-center gap-3 p-3 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg">
                                        @svg('lucide-settings', 'w-[18px] h-[18px] text-gray-500')
                                        <span class="text-gray-700 dark:text-gray-300">{{ __('accountSettings') }}</span>
                                    </a>
                                    <a href="#" class="flex items-center gap-3 p-3 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg">
                                        @svg('lucide-lock', 'w-[18px] h-[18px] text-gray-500')
                                        <span class="text-gray-700 dark:text-gray-300">{{ __('changePassword') }}</span>
                                    </a>
                                    <a href="/patient-portal/logout"
                                       class="w-full flex items-center gap-3 p-3 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg text-red-500">
                                        @svg('lucide-log-out', 'w-[18px] h-[18px]')
                                        <span>{{ __('logout') }}</span>
                                    </a>
                                </div>
                            </x-ui.card-content>
                        </x-ui.card>
                    </div>
                </div>
            </div>
        </section>
    </div>
    @endif
</div>
@endsection
