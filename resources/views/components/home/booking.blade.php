@props(['branches' => [], 'services' => []])
@php $contact = config('clinic.contact'); @endphp
{{-- Mirrors BookingSection.tsx: submit simulates a 2s request, then swaps to a success card. --}}
<section class="section-padding bg-gray-50 dark:bg-gray-900" dir="rtl"
         x-data="{ submitting: false, success: false, submit() { this.submitting = true; setTimeout(() => { this.submitting = false; this.success = true }, 2000) } }">
    <div class="container-custom">
        {{-- Success state --}}
        <div x-show="success" x-cloak>
            <x-ui.card class="max-w-2xl mx-auto text-center">
                <x-ui.card-content class="py-16">
                    <div class="w-20 h-20 mx-auto mb-6 rounded-full bg-green-100 flex items-center justify-center">
                        @svg('lucide-check-circle', 'w-12 h-12 text-green-500')
                    </div>
                    <h3 class="text-2xl font-bold text-gray-800 dark:text-white mb-4">تم استلام طلب الحجز بنجاح!</h3>
                    <p class="text-gray-600 dark:text-gray-400 mb-8">سيتواصل معك فريقنا خلال ساعات العمل لتأكيد موعدك.</p>
                    <x-ui.button variant="primary" x-on:click="success = false">حجز موعد آخر</x-ui.button>
                </x-ui.card-content>
            </x-ui.card>
        </div>

        <div class="grid lg:grid-cols-2 gap-12" x-show="!success">
            {{-- Info Section --}}
            <div>
                <span class="inline-block text-medical-blue dark:text-light-gold font-semibold mb-4">احجز موعدك</span>
                <h2 class="heading-primary mb-6">نحن هنا لمساعدتك</h2>
                <p class="text-gray-600 dark:text-gray-400 mb-8 leading-relaxed">
                    احجزي موعدك الآن واستمتعي برعاية طبية متميزة. فريقنا جاهز للرد على استفساراتك ومساعدتك في اختيار الموعد المناسب.
                </p>

                <div class="space-y-4">
                    <div class="flex items-center gap-4 p-4 bg-white dark:bg-gray-800 rounded-xl shadow-sm">
                        <div class="w-12 h-12 rounded-full bg-medical-blue/10 flex items-center justify-center flex-shrink-0">
                            @svg('lucide-phone', 'w-6 h-6 text-medical-blue')
                        </div>
                        <div>
                            <p class="font-semibold text-gray-800 dark:text-white">اتصل بنا</p>
                            <a href="tel:{{ $contact['phone_tel'] }}" class="text-medical-blue">{{ $contact['phone_display'] }}</a>
                        </div>
                    </div>

                    <div class="flex items-center gap-4 p-4 bg-white dark:bg-gray-800 rounded-xl shadow-sm">
                        <div class="w-12 h-12 rounded-full bg-green-100 flex items-center justify-center flex-shrink-0">
                            @svg('lucide-message-square', 'w-6 h-6 text-green-600')
                        </div>
                        <div>
                            <p class="font-semibold text-gray-800 dark:text-white">واتساب</p>
                            <a href="https://wa.me/{{ $contact['whatsapp'] }}" class="text-green-600">{{ $contact['phone_display'] }}</a>
                        </div>
                    </div>

                    <div class="flex items-center gap-4 p-4 bg-white dark:bg-gray-800 rounded-xl shadow-sm">
                        <div class="w-12 h-12 rounded-full bg-light-gold/10 flex items-center justify-center flex-shrink-0">
                            @svg('lucide-clock', 'w-6 h-6 text-light-gold')
                        </div>
                        <div>
                            <p class="font-semibold text-gray-800 dark:text-white">ساعات العمل</p>
                            <p class="text-gray-600 dark:text-gray-400">{{ $contact['hours_short'] }}</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-4 p-4 bg-white dark:bg-gray-800 rounded-xl shadow-sm">
                        <div class="w-12 h-12 rounded-full bg-pink-100 flex items-center justify-center flex-shrink-0">
                            @svg('lucide-map-pin', 'w-6 h-6 text-pink-600')
                        </div>
                        <div>
                            <p class="font-semibold text-gray-800 dark:text-white">الموقع</p>
                            <p class="text-gray-600 dark:text-gray-400">شارع التحرير، الدقي، الجيزة</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Booking Form --}}
            <x-ui.card class="shadow-xl">
                <x-ui.card-header>
                    <h3 class="text-xl font-bold text-gray-800 dark:text-white">نموذج الحجز السريع</h3>
                </x-ui.card-header>
                <x-ui.card-content>
                    <form class="space-y-5" @submit.prevent="submit()">
                        <x-ui.input label="الاسم الكامل" name="name" placeholder="أدخلي اسمك الكامل" required>
                            <x-slot:leftIcon>@svg('lucide-user', 'w-[18px] h-[18px]')</x-slot:leftIcon>
                        </x-ui.input>

                        <x-ui.input label="رقم الهاتف" name="phone" type="tel" placeholder="01xxxxxxxxx" required>
                            <x-slot:leftIcon>@svg('lucide-phone', 'w-[18px] h-[18px]')</x-slot:leftIcon>
                        </x-ui.input>

                        <x-ui.input label="البريد الإلكتروني" name="email" type="email" placeholder="example@email.com">
                            <x-slot:leftIcon>@svg('lucide-mail', 'w-[18px] h-[18px]')</x-slot:leftIcon>
                        </x-ui.input>

                        <x-ui.select label="الفرع" name="branch" :options="$branches" placeholder="اختاري الفرع" required />
                        <x-ui.select label="نوع الخدمة" name="service" :options="$services" placeholder="اختاري الخدمة" required />

                        <div class="grid grid-cols-2 gap-4">
                            <x-ui.input label="التاريخ المفضل" name="date" type="date" required />
                            <x-ui.input label="الوقت المفضل" name="time" type="time" />
                        </div>

                        <x-ui.textarea label="ملاحظات إضافية" name="notes" rows="3" placeholder="أي ملاحظات أو استفسارات..." />

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
                            تأكيد الحجز
                        </button>

                        <p class="text-center text-sm text-gray-500 dark:text-gray-400">سيتم التواصل معك لتأكيد الموعد</p>
                    </form>
                </x-ui.card-content>
            </x-ui.card>
        </div>
    </div>
</section>
