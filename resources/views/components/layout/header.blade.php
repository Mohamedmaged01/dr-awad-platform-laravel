@php
    $current = '/' . ltrim(request()->path(), '/');
    $nav = config('clinic.navigation');
    $contact = config('clinic.contact');
    $locale = app()->getLocale();
@endphp
<header x-data="{ scrolled: false, menuOpen: false }"
        @scroll.window="scrolled = window.scrollY > 50"
        :class="scrolled ? 'bg-white/95 dark:bg-gray-900/95 backdrop-blur-md shadow-lg' : 'bg-transparent'"
        class="fixed top-0 left-0 right-0 z-50 transition-all duration-300">

    {{-- Top Bar --}}
    <div class="hidden md:block bg-medical-blue text-white py-2">
        <div class="container-custom flex justify-between items-center text-sm">
            <div class="flex items-center gap-4">
                <a href="tel:{{ $contact['phone_tel'] }}" class="flex items-center gap-2 hover:text-light-gold transition-colors">
                    @svg('lucide-phone', 'w-3.5 h-3.5')
                    <span>{{ $contact['phone_display'] }}</span>
                </a>
            </div>
            <div class="flex items-center gap-4">
                <span>{{ __('hoursDaily') }}</span>
            </div>
        </div>
    </div>

    {{-- Main Navigation --}}
    <div class="container-custom">
        <div class="flex items-center justify-between h-20">
            {{-- Logo --}}
            <a href="/" class="flex items-center gap-3">
                <div class="w-14 h-14 rounded-full shadow-md overflow-hidden bg-white ring-1 ring-black/5 flex items-center justify-center">
                    <img src="{{ asset('images/brand-logo.png') }}" alt="{{ __('heroTitle') }}" class="w-full h-full object-contain p-1">
                </div>
                <div :class="scrolled ? 'text-gray-800 dark:text-white' : 'text-white'">
                    <h1 class="font-bold text-lg leading-tight">{{ __('heroTitle') }}</h1>
                    <p class="text-xs opacity-80">{{ __('doctorTitleShort') }}</p>
                </div>
            </a>

            {{-- Desktop Navigation --}}
            <nav class="hidden lg:flex items-center gap-8">
                @foreach ($nav as $item)
                    @php $active = $current === $item['href']; @endphp
                    <a href="{{ $item['href'] }}"
                       @if ($active)
                           class="font-medium transition-colors relative py-2 text-light-gold"
                       @else
                           class="font-medium transition-colors relative py-2"
                           :class="scrolled ? 'text-gray-700 dark:text-gray-200 hover:text-medical-blue dark:hover:text-light-gold' : 'text-white/90 hover:text-white'"
                       @endif>
                        {{ __($item['name']) }}
                        @if ($active)
                            <span class="absolute bottom-0 left-0 right-0 h-0.5 bg-light-gold rounded-full"></span>
                        @endif
                    </a>
                @endforeach
            </nav>

            {{-- Action Buttons --}}
            <div class="hidden lg:flex items-center gap-3">
                {{-- Language switcher (AR / EN) --}}
                <div class="flex items-center gap-0.5 ps-2 pe-1 py-1 rounded-full border shadow-sm transition-all duration-300"
                     :class="scrolled ? 'bg-gray-100 border-gray-200 dark:bg-gray-800 dark:border-gray-700' : 'bg-white/10 border-white/25 backdrop-blur-md'">
                    <span class="me-0.5" :class="scrolled ? 'text-gray-400 dark:text-gray-500' : 'text-white/70'">@svg('lucide-globe', 'w-4 h-4')</span>
                    <a href="/locale/ar"
                       @class([
                           'px-2.5 py-1 rounded-full text-xs font-bold transition-all duration-300',
                           'bg-white text-medical-blue shadow-sm dark:bg-medical-blue dark:text-white' => $locale === 'ar',
                       ])
                       @if ($locale !== 'ar') :class="scrolled ? 'text-gray-500 hover:text-medical-blue dark:text-gray-400' : 'text-white/70 hover:text-white'" @endif>
                        عربي
                    </a>
                    <a href="/locale/en"
                       @class([
                           'px-2.5 py-1 rounded-full text-xs font-bold transition-all duration-300',
                           'bg-white text-medical-blue shadow-sm dark:bg-medical-blue dark:text-white' => $locale === 'en',
                       ])
                       @if ($locale !== 'en') :class="scrolled ? 'text-gray-500 hover:text-medical-blue dark:text-gray-400' : 'text-white/70 hover:text-white'" @endif>
                        EN
                    </a>
                </div>

                <button @click="$store.theme.toggle()"
                        class="p-2 rounded-lg transition-colors"
                        :class="scrolled ? 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800' : 'text-white/80 hover:text-white hover:bg-white/10'"
                        aria-label="Toggle theme">
                    <span x-show="!$store.theme.dark">@svg('lucide-moon', 'w-5 h-5')</span>
                    <span x-show="$store.theme.dark" x-cloak>@svg('lucide-sun', 'w-5 h-5')</span>
                </button>

                <a href="/patient-portal"
                   class="inline-flex items-center justify-center font-semibold rounded-lg transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-offset-2 px-3 py-1.5 text-sm gap-1.5 border-2"
                   :class="scrolled ? 'border-medical-blue text-medical-blue hover:bg-medical-blue hover:text-white focus:ring-medical-blue' : 'border-white/50 text-white hover:bg-white hover:text-medical-blue focus:ring-medical-blue'">
                    @svg('lucide-user', 'w-[18px] h-[18px]')
                    {{ __('patientPortal') }}
                </a>

                <x-ui.button href="/booking" variant="gold" size="sm">
                    <x-slot:leftIcon>@svg('lucide-calendar', 'w-[18px] h-[18px]')</x-slot:leftIcon>
                    {{ __('booking') }}
                </x-ui.button>

                <a href="/admin/login" title="{{ __('adminLogin') }}"
                   class="p-2 rounded-lg transition-colors border-2"
                   :class="scrolled ? 'border-gray-100 dark:border-gray-700 text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-medical-blue' : 'border-white/20 text-white/70 hover:bg-white/10 hover:text-white'">
                    @svg('lucide-lock', 'w-[18px] h-[18px]')
                </a>
            </div>

            {{-- Mobile Menu Button --}}
            <button @click="menuOpen = !menuOpen"
                    class="lg:hidden p-2 rounded-lg"
                    :class="scrolled ? 'text-gray-600 dark:text-gray-300' : 'text-white'">
                <span x-show="!menuOpen">@svg('lucide-menu', 'w-6 h-6')</span>
                <span x-show="menuOpen" x-cloak>@svg('lucide-x', 'w-6 h-6')</span>
            </button>
        </div>
    </div>

    {{-- Mobile Menu --}}
    <div x-show="menuOpen" x-cloak class="lg:hidden bg-white dark:bg-gray-900 border-t dark:border-gray-800 shadow-lg">
        <div class="container-custom py-4">
            <nav class="flex flex-col gap-2">
                @foreach ($nav as $item)
                    @php $active = $current === $item['href']; @endphp
                    <a href="{{ $item['href'] }}"
                       @click="menuOpen = false"
                       @class([
                           'px-4 py-3 rounded-lg font-medium transition-colors',
                           'bg-medical-blue text-white' => $active,
                           'text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-800' => ! $active,
                       ])>
                        {{ __($item['name']) }}
                    </a>
                @endforeach
                <hr class="my-2 border-gray-200 dark:border-gray-700">
                <a href="/patient-portal" @click="menuOpen = false"
                   class="px-4 py-3 rounded-lg font-medium text-medical-blue dark:text-light-gold">
                    {{ __('patientPortal') }}
                </a>
                <a href="/booking" @click="menuOpen = false">
                    <x-ui.button variant="gold" class="w-full mt-2">
                        <x-slot:leftIcon>@svg('lucide-calendar', 'w-[18px] h-[18px]')</x-slot:leftIcon>
                        {{ __('booking') }}
                    </x-ui.button>
                </a>
                <a href="/admin/login" @click="menuOpen = false"
                   class="w-full flex items-center justify-center gap-2 mt-2 px-4 py-3 rounded-lg border-2 border-gray-100 dark:border-gray-700 text-gray-500 dark:text-gray-300 font-medium">
                    @svg('lucide-lock', 'w-[18px] h-[18px]')
                    {{ __('adminLogin') }}
                </a>
                {{-- Language switcher (AR / EN) --}}
                <div class="flex items-center gap-1 p-1 bg-gray-100 dark:bg-gray-800 rounded-full mt-4 border border-gray-200 dark:border-gray-700">
                    <span class="ps-3 text-gray-400 dark:text-gray-500">@svg('lucide-globe', 'w-[18px] h-[18px]')</span>
                    <a href="/locale/ar"
                       @class([
                           'flex-1 flex items-center justify-center gap-2 py-2 rounded-full text-sm font-bold transition-all duration-300',
                           'bg-white dark:bg-medical-blue text-medical-blue dark:text-white shadow-sm' => $locale === 'ar',
                           'text-gray-500 dark:text-gray-400' => $locale !== 'ar',
                       ])>
                        العربية
                    </a>
                    <a href="/locale/en"
                       @class([
                           'flex-1 flex items-center justify-center gap-2 py-2 rounded-full text-sm font-bold transition-all duration-300',
                           'bg-white dark:bg-medical-blue text-medical-blue dark:text-white shadow-sm' => $locale === 'en',
                           'text-gray-500 dark:text-gray-400' => $locale !== 'en',
                       ])>
                        English
                    </a>
                </div>

                <div class="flex items-center justify-between mt-4 px-4">
                    <button @click="$store.theme.toggle()" class="flex items-center gap-2 text-gray-600 dark:text-gray-300">
                        <span x-show="!$store.theme.dark" class="flex items-center gap-2">@svg('lucide-moon', 'w-5 h-5') <span>{{ __('darkMode') }}</span></span>
                        <span x-show="$store.theme.dark" x-cloak class="flex items-center gap-2">@svg('lucide-sun', 'w-5 h-5') <span>{{ __('lightMode') }}</span></span>
                    </button>
                </div>
            </nav>
        </div>
    </div>
</header>
