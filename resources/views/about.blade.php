@extends('layouts.public')

@section('title', 'عن الدكتور محمد عوض | استشاري النساء والتوليد')

@section('content')
    {{-- Hero Section --}}
    <section class="pt-32 pb-16 gradient-medical">
        <div class="container-custom">
            <div class="grid lg:grid-cols-2 gap-12 items-center">
                <div class="text-white">
                    <h1 class="text-4xl md:text-5xl font-bold mb-6">د. محمد عوض</h1>
                    <p class="text-xl text-light-gold mb-4">استشاري النساء والتوليد والحقن المجهري وجراحات المناظير</p>
                    <p class="text-white/80 text-lg leading-relaxed mb-8">
                        خبرة تزيد عن 20 عاماً في مجال النساء والتوليد وعلاج العقم. حاصل على درجة الدكتوراه من جامعة القاهرة وزمالة الكلية الملكية البريطانية.
                    </p>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        @foreach ($achievements as $item)
                            <div class="text-center p-4 bg-white/10 rounded-xl backdrop-blur-sm">
                                <div class="text-2xl md:text-3xl font-bold text-light-gold">{{ $item['value'] }}</div>
                                <div class="text-sm text-white/70">{{ $item['label'] }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="hidden lg:block">
                    <div class="relative">
                        <div class="aspect-square rounded-3xl overflow-hidden border-8 border-white/20 shadow-2xl">
                            <img src="/images/dr-mohamed-awad.jpg" alt="Dr. Mohamed Awad" class="w-full h-full object-cover">
                        </div>
                        <div class="absolute -bottom-4 -right-4 bg-light-gold text-white rounded-2xl p-4 shadow-xl">
                            <div class="flex items-center gap-2">
                                @svg('lucide-star', 'w-5 h-5 fill-white')
                                <span class="font-bold">4.9</span>
                            </div>
                            <p class="text-sm">تقييم المرضى</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Qualifications & Experience --}}
    <section class="py-16">
        <div class="container-custom">
            <div class="grid lg:grid-cols-2 gap-12">
                <div>
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-12 h-12 rounded-xl bg-medical-blue/10 flex items-center justify-center">
                            @svg('lucide-graduation-cap', 'w-6 h-6 text-medical-blue')
                        </div>
                        <h2 class="text-2xl font-bold text-gray-800 dark:text-white">المؤهلات العلمية</h2>
                    </div>
                    <ul class="space-y-3">
                        @foreach ($qualifications as $item)
                            <li class="flex items-start gap-3 p-4 bg-gray-50 dark:bg-gray-800 rounded-xl">
                                @svg('lucide-check-circle', 'w-5 h-5 text-green-500 flex-shrink-0 mt-0.5')
                                <span class="text-gray-700 dark:text-gray-300">{{ $item }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <div>
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-12 h-12 rounded-xl bg-light-gold/10 flex items-center justify-center">
                            @svg('lucide-building', 'w-6 h-6 text-light-gold')
                        </div>
                        <h2 class="text-2xl font-bold text-gray-800 dark:text-white">الخبرات المهنية</h2>
                    </div>
                    <ul class="space-y-3">
                        @foreach ($experience as $item)
                            <li class="flex items-start gap-3 p-4 bg-gray-50 dark:bg-gray-800 rounded-xl">
                                @svg('lucide-award', 'w-5 h-5 text-light-gold flex-shrink-0 mt-0.5')
                                <span class="text-gray-700 dark:text-gray-300">{{ $item }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </section>

    {{-- Memberships --}}
    <section class="py-16 bg-gray-50 dark:bg-gray-900">
        <div class="container-custom">
            <div class="text-center mb-12">
                <div class="w-16 h-16 rounded-full bg-medical-blue/10 flex items-center justify-center mx-auto mb-4">
                    @svg('lucide-globe', 'w-8 h-8 text-medical-blue')
                </div>
                <h2 class="text-3xl font-bold text-gray-800 dark:text-white mb-4">العضويات الدولية والمحلية</h2>
                <p class="text-gray-600 dark:text-gray-400">عضوية فعالة في أهم الجمعيات الطبية العالمية والمحلية</p>
            </div>
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($memberships as $item)
                    <div class="flex items-center gap-4 p-6 bg-white dark:bg-gray-800 rounded-xl shadow-md">
                        <div class="w-12 h-12 rounded-full bg-gradient-to-br from-medical-blue to-light-gold flex items-center justify-center text-white flex-shrink-0">
                            @svg('lucide-award', 'w-5 h-5')
                        </div>
                        <span class="text-gray-700 dark:text-gray-300 font-medium">{{ $item }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Why Choose Us --}}
    <section class="py-16">
        <div class="container-custom">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-800 dark:text-white mb-4">لماذا تختارين د. محمد عوض؟</h2>
            </div>
            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach ($whyChooseUs as $item)
                    <div class="text-center p-6 bg-white dark:bg-gray-800 rounded-xl shadow-md">
                        <div class="w-16 h-16 rounded-full bg-medical-blue/10 flex items-center justify-center mx-auto mb-4">
                            @svg('lucide-' . $item['icon'], 'w-7 h-7 text-medical-blue')
                        </div>
                        <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-2">{{ $item['title'] }}</h3>
                        <p class="text-gray-600 dark:text-gray-400">{{ $item['desc'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- CTA --}}
    <section class="py-16 gradient-medical">
        <div class="container-custom text-center text-white">
            <h2 class="text-3xl font-bold mb-4">احجزي موعدك الآن</h2>
            <p class="text-white/80 text-lg mb-8 max-w-2xl mx-auto">
                نحن هنا لمساعدتك في رحلتك نحو الأمومة أو للعناية بصحتك النسائية
            </p>
            <a href="/booking" class="inline-flex items-center gap-2 bg-light-gold text-white px-8 py-4 rounded-lg font-semibold hover:bg-light-gold-light transition-colors text-lg">
                @svg('lucide-calendar', 'w-5 h-5')
                احجزي موعد
            </a>
        </div>
    </section>
@endsection
