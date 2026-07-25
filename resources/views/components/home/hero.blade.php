<section class="relative min-h-screen flex items-center pt-32 pb-16 overflow-hidden">
    {{-- Background --}}
    <div class="absolute inset-0 gradient-medical"></div>
    <div class="absolute inset-0 bg-[url('/images/pattern.svg')] opacity-10"></div>

    {{-- Decorative Elements --}}
    <div class="absolute top-20 right-10 w-72 h-72 bg-light-gold/20 rounded-full blur-3xl animate-float"></div>
    <div class="absolute bottom-20 left-10 w-96 h-96 bg-soft-pink/20 rounded-full blur-3xl animate-float" style="animation-delay: 1s"></div>

    <div class="container-custom relative z-10">
        <div class="grid lg:grid-cols-2 gap-12 items-center">
            {{-- Content --}}
            <div class="text-white animate-fade-in">
                <div class="inline-flex items-center gap-2 bg-white/10 backdrop-blur-sm rounded-full px-4 py-2 mb-6">
                    @svg('lucide-sparkles', 'w-4 h-4 text-light-gold')
                    <span class="text-sm">{{ __('heroEyebrow') }}</span>
                </div>

                <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold mb-6 leading-tight">
                    <span class="text-light-gold">{{ __('heroTitle') }}</span>
                    <br>
                    {{ __('heroSubtitle') }}
                </h1>

                <p class="text-lg md:text-xl text-white/80 mb-8 leading-relaxed max-w-xl">
                    {{ __('heroDescription') }}
                </p>

                <div class="flex flex-wrap gap-4">
                    <x-ui.button href="/booking" variant="gold" size="lg">
                        <x-slot:leftIcon>@svg('lucide-calendar', 'w-5 h-5')</x-slot:leftIcon>
                        {{ __('bookNow') }}
                    </x-ui.button>

                    <x-ui.button href="tel:{{ config('clinic.contact.phone_tel') }}" variant="outline" size="lg"
                                 class="border-white/50 text-white hover:bg-white hover:text-medical-blue">
                        <x-slot:leftIcon>@svg('lucide-phone', 'w-5 h-5')</x-slot:leftIcon>
                        {{ __('callNow') }}
                    </x-ui.button>

                    <x-ui.button href="https://wa.me/{{ config('clinic.contact.whatsapp') }}" target="_blank" rel="noopener noreferrer"
                                 variant="secondary" size="lg" class="bg-green-500 text-white hover:bg-green-600 border-none">
                        <x-slot:leftIcon>@svg('lucide-message-circle', 'w-5 h-5')</x-slot:leftIcon>
                        {{ __('whatsapp') }}
                    </x-ui.button>
                </div>
            </div>

            {{-- Image Section --}}
            <div class="relative hidden lg:block">
                <div class="relative w-full aspect-square max-w-lg mx-auto">
                    {{-- Main Image Circle --}}
                    <div class="absolute inset-8 rounded-full bg-white shadow-2xl overflow-hidden animate-pulse-glow border-4 border-white/50">
                        <img src="/images/dr-mohamed-awad.jpg" alt="د. محمد عوض" class="w-full h-full object-cover">
                    </div>

                    {{-- Video Play Button --}}
                    <button class="absolute bottom-1/3 right-1/4 w-16 h-16 bg-light-gold rounded-full shadow-lg flex items-center justify-center text-white hover:scale-110 transition-transform">
                        @svg('lucide-play', 'w-6 h-6 fill-white')
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Scroll Indicator --}}
    <div class="absolute bottom-8 left-1/2 -translate-x-1/2 animate-bounce">
        <div class="w-6 h-10 border-2 border-white/50 rounded-full flex items-start justify-center p-2">
            <div class="w-1.5 h-3 bg-white/70 rounded-full"></div>
        </div>
    </div>
</section>
