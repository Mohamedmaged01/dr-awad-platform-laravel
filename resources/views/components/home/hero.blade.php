@props(['stats' => []])
<section class="relative min-h-screen flex items-center pt-32 pb-16 overflow-hidden" dir="rtl">
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
                    <span class="text-sm">مرحباً بكِ في عيادة د. محمد عوض</span>
                </div>

                <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold mb-6 leading-tight">
                    <span class="text-light-gold">د. محمد عوض</span>
                    <br>
                    استشاري النساء والتوليد
                    <br>
                    والحقن المجهري
                </h1>

                <p class="text-lg md:text-xl text-white/80 mb-8 leading-relaxed max-w-xl">
                    نقدم أعلى معايير الرعاية الطبية للمرأة في مجالات النساء والتوليد، علاج العقم والحقن المجهري، وجراحات المناظير المتقدمة بأحدث التقنيات العالمية.
                </p>

                <div class="flex flex-wrap gap-4 mb-12">
                    <x-ui.button href="/booking" variant="gold" size="lg">
                        <x-slot:leftIcon>@svg('lucide-calendar', 'w-5 h-5')</x-slot:leftIcon>
                        احجز موعدك الآن
                    </x-ui.button>

                    <x-ui.button href="tel:{{ config('clinic.contact.phone_tel') }}" variant="outline" size="lg"
                                 class="border-white/50 text-white hover:bg-white hover:text-medical-blue">
                        <x-slot:leftIcon>@svg('lucide-phone', 'w-5 h-5')</x-slot:leftIcon>
                        اتصل بنا
                    </x-ui.button>

                    <x-ui.button href="https://wa.me/{{ config('clinic.contact.whatsapp') }}" target="_blank" rel="noopener noreferrer"
                                 variant="secondary" size="lg" class="bg-green-500 text-white hover:bg-green-600 border-none">
                        <x-slot:leftIcon>@svg('lucide-message-circle', 'w-5 h-5')</x-slot:leftIcon>
                        واتساب
                    </x-ui.button>
                </div>

                {{-- Stats --}}
                <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                    @foreach ($stats as $stat)
                        <div class="text-center p-4 bg-white/10 backdrop-blur-sm rounded-xl">
                            @svg('lucide-' . $stat['icon'], 'w-6 h-6 mx-auto mb-2 text-light-gold')
                            <div class="text-2xl md:text-3xl font-bold text-white mb-1">{{ $stat['value'] }}</div>
                            <div class="text-sm text-white/70">{{ $stat['label'] }}</div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Image Section --}}
            <div class="relative hidden lg:block">
                <div class="relative w-full aspect-square max-w-lg mx-auto">
                    {{-- Main Image Circle --}}
                    <div class="absolute inset-8 rounded-full bg-white shadow-2xl overflow-hidden animate-pulse-glow border-4 border-white/50">
                        <img src="/images/dr-mohamed-awad.jpg" alt="د. محمد عوض" class="w-full h-full object-cover">
                    </div>

                    {{-- Floating Elements --}}
                    <div class="absolute top-0 right-0 bg-white rounded-2xl shadow-xl p-4 animate-float">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center">
                                @svg('lucide-heart', 'w-5 h-5 text-green-600')
                            </div>
                            <div>
                                <p class="font-bold text-gray-800">معدل النجاح</p>
                                <p class="text-sm text-green-600">70%+</p>
                            </div>
                        </div>
                    </div>

                    <div class="absolute bottom-10 left-0 bg-white rounded-2xl shadow-xl p-4 animate-float" style="animation-delay: 0.5s">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center">
                                @svg('lucide-award', 'w-5 h-5 text-medical-blue')
                            </div>
                            <div>
                                <p class="font-bold text-gray-800">خبرة</p>
                                <p class="text-sm text-medical-blue">+20 سنة</p>
                            </div>
                        </div>
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
