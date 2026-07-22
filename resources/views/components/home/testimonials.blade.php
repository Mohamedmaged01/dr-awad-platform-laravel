@props(['testimonials' => [], 'stats' => []])
<section class="section-padding bg-gradient-to-br from-soft-pink to-white dark:from-gray-800 dark:to-gray-900" dir="rtl"
         x-data="{ i: 0, items: @js($testimonials), next() { this.i = (this.i + 1) % this.items.length }, prev() { this.i = (this.i - 1 + this.items.length) % this.items.length } }">
    <div class="container-custom">
        {{-- Section Header --}}
        <div class="text-center max-w-3xl mx-auto mb-16">
            <span class="inline-block text-medical-blue dark:text-light-gold font-semibold mb-4">آراء المريضات</span>
            <h2 class="heading-primary mb-6">قصص نجاح ملهمة</h2>
            <p class="text-gray-600 dark:text-gray-400 text-lg">
                آلاف السيدات حققن حلمهن بالأمومة وتغلبن على التحديات الصحية بفضل الله ثم بفضل خبرتنا ورعايتنا المتميزة.
            </p>
        </div>

        {{-- Testimonials Slider --}}
        <div class="relative max-w-4xl mx-auto">
            {{-- RTL: the start-side arrow advances, matching the source. --}}
            <button @click="next()"
                    class="absolute top-1/2 -translate-y-1/2 -start-4 md:-start-12 z-10 w-12 h-12 rounded-full bg-white dark:bg-gray-800 shadow-lg flex items-center justify-center text-medical-blue hover:bg-medical-blue hover:text-white transition-colors">
                @svg('lucide-chevron-right', 'w-6 h-6')
            </button>
            <button @click="prev()"
                    class="absolute top-1/2 -translate-y-1/2 -end-4 md:-end-12 z-10 w-12 h-12 rounded-full bg-white dark:bg-gray-800 shadow-lg flex items-center justify-center text-medical-blue hover:bg-medical-blue hover:text-white transition-colors">
                @svg('lucide-chevron-left', 'w-6 h-6')
            </button>

            {{-- Main Testimonial --}}
            <x-ui.card class="overflow-hidden">
                <x-ui.card-content class="p-8 md:p-12">
                    @svg('lucide-quote', 'w-12 h-12 text-medical-blue/20 mb-6')

                    <p class="text-xl md:text-2xl text-gray-700 dark:text-gray-300 leading-relaxed mb-8" x-text="items[i].content"></p>

                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <div class="w-14 h-14 rounded-full bg-gradient-to-br from-medical-blue to-light-gold flex items-center justify-center text-white font-bold text-xl"
                                 x-text="items[i].name.charAt(0)"></div>
                            <div>
                                <h4 class="font-bold text-gray-800 dark:text-white" x-text="items[i].name"></h4>
                                <p class="text-sm text-gray-500 dark:text-gray-400" x-text="items[i].location"></p>
                            </div>
                        </div>

                        <div class="text-end">
                            <div class="flex items-center gap-1 mb-1">
                                <template x-for="n in items[i].rating" :key="n">
                                    <span>@svg('lucide-star', 'w-[18px] h-[18px] text-yellow-400 fill-yellow-400')</span>
                                </template>
                            </div>
                            <span class="text-sm text-medical-blue dark:text-light-gold font-medium" x-text="items[i].service"></span>
                        </div>
                    </div>
                </x-ui.card-content>
            </x-ui.card>

            {{-- Dots Navigation --}}
            <div class="flex justify-center gap-2 mt-8">
                @foreach ($testimonials as $index => $t)
                    <button @click="i = {{ $index }}"
                            :class="i === {{ $index }} ? 'bg-medical-blue w-8' : 'bg-gray-300 dark:bg-gray-600 hover:bg-gray-400'"
                            class="w-3 h-3 rounded-full transition-all"></button>
                @endforeach
            </div>
        </div>

        {{-- Stats --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mt-16">
            @foreach ($stats as $stat)
                <div class="text-center p-6 bg-white dark:bg-gray-800 rounded-2xl shadow-md">
                    <div class="text-3xl md:text-4xl font-bold text-medical-blue dark:text-light-gold mb-2">{{ $stat['value'] }}</div>
                    <div class="text-gray-600 dark:text-gray-400">{{ $stat['label'] }}</div>
                </div>
            @endforeach
        </div>
    </div>
</section>
