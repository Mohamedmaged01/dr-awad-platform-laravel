@php
    $locale = app()->getLocale();
    $dir = $locale === 'ar' ? 'rtl' : 'ltr';
    $bodyFont = $locale === 'ar' ? 'font-cairo' : 'font-poppins';
    $roleLabels = config('clinic.role_labels');
    // Real seeded accounts (all password: "password").
    $quickAccounts = [
        'admin' => 'admin@dr-awad.com',
        'doctor' => 'dr.mohamed@dr-awad.com',
        'nurse' => 'nurse@dr-awad.com',
        'receptionist' => 'reception@dr-awad.com',
        'lab_technician' => 'lab@dr-awad.com',
    ];
@endphp
<!DOCTYPE html>
<html lang="{{ $locale }}" dir="{{ $dir }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('adminLoginTitle') }} | {{ __('heroTitle') }}</title>
    <script>
        (function () {
            var t = localStorage.getItem('theme');
            if (t === 'dark' || (!t && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            }
        })();
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="{{ $bodyFont }} antialiased">
<div class="min-h-screen flex items-center justify-center gradient-soft dark:bg-gray-900 p-4"
     x-data="{ email: @js(old('email', '')), password: '', showPassword: false }">
    <div class="w-full max-w-md">
        {{-- Logo --}}
        <div class="text-center mb-8">
            <div class="w-32 h-32 mx-auto mb-4 rounded-3xl bg-slate-900 shadow-xl flex items-center justify-center animate-pulse-glow ring-4 ring-medical-blue/20">
                <img src="{{ asset('images/brand-logo.png') }}" alt="{{ __('heroTitle') }}" class="w-full h-full object-contain p-1">
            </div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white">{{ __('adminLoginTitle') }}</h1>
            <p class="text-gray-500 dark:text-gray-400 mt-1">{{ __('adminLoginSubtitle') }}</p>
        </div>

        <x-ui.card class="shadow-xl">
            <x-ui.card-content class="p-8">
                @if (session('timeout_message'))
                    <div class="mb-6 p-3 rounded-lg bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300 text-sm">
                        {{ session('timeout_message') }}
                    </div>
                @endif

                @error('email')
                    <div class="mb-6 p-3 rounded-lg bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300 text-sm">
                        {{ $message }}
                    </div>
                @enderror

                <form method="POST" action="/admin/login" class="space-y-5">
                    @csrf
                    <x-ui.input :label="__('email')" name="email" type="email" x-model="email"
                                autocomplete="username" placeholder="admin@dr-awad.com" required>
                        <x-slot:leftIcon>@svg('lucide-mail', 'w-[18px] h-[18px]')</x-slot:leftIcon>
                    </x-ui.input>

                    <div class="relative">
                        <x-ui.input :label="__('password')" name="password" type="password" ::type="showPassword ? 'text' : 'password'"
                                    x-model="password" autocomplete="current-password" required placeholder="••••••••">
                            <x-slot:leftIcon>@svg('lucide-lock', 'w-[18px] h-[18px]')</x-slot:leftIcon>
                        </x-ui.input>
                        <button type="button" @click="showPassword = !showPassword"
                                class="absolute bottom-3 end-3 text-gray-400 hover:text-gray-600">
                            <span x-show="!showPassword">@svg('lucide-eye', 'w-[18px] h-[18px]')</span>
                            <span x-show="showPassword" x-cloak>@svg('lucide-eye-off', 'w-[18px] h-[18px]')</span>
                        </button>
                    </div>

                    <x-ui.button type="submit" variant="primary" size="lg" class="w-full">
                        <x-slot:leftIcon>@svg('lucide-log-in', 'w-[18px] h-[18px]')</x-slot:leftIcon>
                        {{ __('loginBtn') }}
                    </x-ui.button>
                </form>

                {{-- Quick-access role grid (demo) --}}
                <div class="mt-6 pt-6 border-t border-gray-100 dark:border-gray-700">
                    <p class="text-center text-xs font-semibold text-gray-500 mb-3">{{ __('quickAccess') }}</p>
                    <div class="grid grid-cols-2 gap-2">
                        @foreach ($quickAccounts as $r => $accEmail)
                            <button type="button"
                                    @click="email = '{{ $accEmail }}'; password = 'password'"
                                    class="px-3 py-2 text-xs font-medium rounded-lg border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-300 hover:bg-medical-blue hover:text-white hover:border-medical-blue transition-colors">
                                {{ __($roleLabels[$r]) }}
                            </button>
                        @endforeach
                    </div>
                </div>
            </x-ui.card-content>
        </x-ui.card>

        <div class="text-center mt-6">
            <a href="/" class="inline-flex items-center gap-2 text-sm text-medical-blue dark:text-light-gold hover:underline">
                @svg('lucide-arrow-' . ($dir === 'rtl' ? 'right' : 'left'), 'w-4 h-4')
                {{ __('backToSite') }}
            </a>
        </div>
    </div>
</div>
</body>
</html>
