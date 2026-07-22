@props(['achievements' => [], 'highlights' => []])
<section class="section-padding" dir="rtl">
    <div class="container-custom">
        <div class="grid lg:grid-cols-2 gap-16 items-center">
            {{-- Image Section --}}
            <div class="relative">
                <div class="relative aspect-[4/5] rounded-2xl overflow-hidden shadow-2xl">
                    <img src="/images/dr-mohamed-awad.jpg" alt="د. محمد عوض" class="w-full h-full object-cover">
                    <div class="absolute inset-0 bg-gradient-to-t from-medical-blue/80 via-transparent to-transparent flex items-end p-8">
                        <div class="text-white">
                            <h3 class="text-2xl font-bold mb-1">د. محمد عوض</h3>
                            <p class="text-white/90 text-sm">استشاري النساء والتوليد والحقن المجهري</p>
                        </div>
                    </div>
                </div>

                {{-- Experience Badge --}}
                <div class="absolute -bottom-6 -left-6 bg-light-gold text-white rounded-2xl p-6 shadow-xl">
                    <div class="text-4xl font-bold">20+</div>
                    <div class="text-sm">سنة خبرة</div>
                </div>

                {{-- Decorative Elements --}}
                <div class="absolute -top-4 -right-4 w-24 h-24 bg-soft-pink rounded-full -z-10"></div>
                <div class="absolute -bottom-8 right-1/4 w-16 h-16 bg-medical-blue/20 rounded-full -z-10"></div>
            </div>

            {{-- Content Section --}}
            <div>
                <span class="inline-block text-medical-blue dark:text-light-gold font-semibold mb-4">عن الدكتور</span>
                <h2 class="heading-primary mb-6">د. محمد عوض</h2>
                <p class="text-xl text-medical-blue dark:text-light-gold font-medium mb-4">
                    استشاري النساء والتوليد والحقن المجهري وجراحات المناظير
                </p>
                <p class="text-gray-600 dark:text-gray-400 mb-8 leading-relaxed">
                    يتمتع الدكتور محمد عوض بخبرة تزيد عن 20 عاماً في مجال النساء والتوليد وعلاج العقم. حاصل على درجة الدكتوراه من جامعة القاهرة وزمالة الكلية الملكية البريطانية، ويعتمد في علاجاته على أحدث التقنيات العالمية والبروتوكولات العلاجية المتطورة.
                </p>

                {{-- Achievements Grid --}}
                <div class="grid grid-cols-2 gap-4 mb-8">
                    @foreach ($achievements as $item)
                        <div class="flex items-center gap-3 p-4 bg-gray-50 dark:bg-gray-800 rounded-xl">
                            <div class="w-12 h-12 rounded-lg bg-medical-blue/10 flex items-center justify-center flex-shrink-0">
                                @svg('lucide-' . $item['icon'], 'w-6 h-6 text-medical-blue')
                            </div>
                            <div>
                                <p class="font-semibold text-gray-800 dark:text-white text-sm">{{ $item['label'] }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $item['desc'] }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Highlights (source renders the first 4) --}}
                <div class="space-y-3 mb-8">
                    @foreach (array_slice($highlights, 0, 4) as $item)
                        <div class="flex items-center gap-3">
                            @svg('lucide-check-circle', 'w-5 h-5 text-green-500 flex-shrink-0')
                            <span class="text-gray-700 dark:text-gray-300">{{ $item }}</span>
                        </div>
                    @endforeach
                </div>

                <x-ui.button href="/about" variant="primary" size="lg">
                    تعرف على المزيد
                    <x-slot:rightIcon>@svg('lucide-arrow-left', 'w-5 h-5')</x-slot:rightIcon>
                </x-ui.button>
            </div>
        </div>
    </div>
</section>
