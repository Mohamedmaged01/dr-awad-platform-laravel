@extends('layouts.public')

@section('title', 'بوابة المريضات | د. محمد عوض')

@section('content')
{{-- Mirrors patient-portal/page.tsx: demo login toggle, then a demo dashboard. --}}
<div x-data="{ loggedIn: false, showLogin: true }">

    {{-- ===== Login / Register ===== --}}
    <section x-show="!loggedIn" class="pt-32 pb-16 min-h-[80vh] flex items-center gradient-soft">
        <div class="container-custom">
            <div class="max-w-md mx-auto">
                <x-ui.card class="shadow-xl">
                    <x-ui.card-content class="p-8">
                        <div class="text-center mb-8">
                            <div class="w-20 h-20 mx-auto mb-4 rounded-full gradient-medical flex items-center justify-center">
                                @svg('lucide-user', 'w-10 h-10 text-white')
                            </div>
                            <h1 class="text-2xl font-bold text-gray-800 dark:text-white mb-2">بوابة المريضات</h1>
                            <p class="text-gray-500" x-text="showLogin ? 'تسجيل الدخول للوصول لملفك الطبي' : 'إنشاء حساب جديد'"></p>
                        </div>

                        <div class="flex rounded-lg border border-gray-200 dark:border-gray-700 mb-6 overflow-hidden">
                            <button @click="showLogin = true" class="flex-1 py-2 text-sm font-medium"
                                    :class="showLogin && 'bg-medical-blue text-white'">تسجيل الدخول</button>
                            <button @click="showLogin = false" class="flex-1 py-2 text-sm font-medium"
                                    :class="!showLogin && 'bg-medical-blue text-white'">حساب جديد</button>
                        </div>

                        {{-- Login --}}
                        <form x-show="showLogin" @submit.prevent="loggedIn = true" class="space-y-5">
                            <x-ui.input label="البريد الإلكتروني أو رقم الهاتف" name="email" placeholder="example@email.com" required>
                                <x-slot:leftIcon>@svg('lucide-mail', 'w-[18px] h-[18px]')</x-slot:leftIcon>
                            </x-ui.input>
                            <x-ui.input label="كلمة المرور" name="password" type="password" placeholder="••••••••" required>
                                <x-slot:leftIcon>@svg('lucide-lock', 'w-[18px] h-[18px]')</x-slot:leftIcon>
                            </x-ui.input>
                            <div class="flex items-center justify-between">
                                <label class="flex items-center gap-2 text-sm">
                                    <input type="checkbox" class="rounded">
                                    <span class="text-gray-600 dark:text-gray-400">تذكرني</span>
                                </label>
                                <a href="#" class="text-sm text-medical-blue hover:underline">نسيت كلمة المرور؟</a>
                            </div>
                            <x-ui.button type="submit" variant="primary" size="lg" class="w-full">
                                <x-slot:leftIcon>@svg('lucide-log-in', 'w-[18px] h-[18px]')</x-slot:leftIcon>
                                تسجيل الدخول
                            </x-ui.button>
                        </form>

                        {{-- Register --}}
                        <form x-show="!showLogin" x-cloak @submit.prevent class="space-y-5">
                            <x-ui.input label="الاسم الكامل" name="reg_name" placeholder="أدخلي اسمك" required>
                                <x-slot:leftIcon>@svg('lucide-user', 'w-[18px] h-[18px]')</x-slot:leftIcon>
                            </x-ui.input>
                            <x-ui.input label="رقم الهاتف" name="reg_phone" type="tel" placeholder="01xxxxxxxxx" required>
                                <x-slot:leftIcon>@svg('lucide-phone', 'w-[18px] h-[18px]')</x-slot:leftIcon>
                            </x-ui.input>
                            <x-ui.input label="البريد الإلكتروني" name="reg_email" type="email" placeholder="example@email.com" required>
                                <x-slot:leftIcon>@svg('lucide-mail', 'w-[18px] h-[18px]')</x-slot:leftIcon>
                            </x-ui.input>
                            <x-ui.input label="كلمة المرور" name="reg_password" type="password" placeholder="••••••••" required>
                                <x-slot:leftIcon>@svg('lucide-lock', 'w-[18px] h-[18px]')</x-slot:leftIcon>
                            </x-ui.input>
                            <x-ui.input label="رقم الملف (إن وجد)" name="fileNumber" placeholder="P2024XXXXX">
                                <x-slot:leftIcon>@svg('lucide-file-text', 'w-[18px] h-[18px]')</x-slot:leftIcon>
                            </x-ui.input>
                            <x-ui.button type="submit" variant="primary" size="lg" class="w-full">إنشاء حساب</x-ui.button>
                        </form>

                        <p class="text-center text-sm text-gray-500 mt-6">
                            بالتسجيل، أوافق على
                            <a href="/terms" class="text-medical-blue hover:underline">الشروط والأحكام</a>
                            و
                            <a href="/privacy" class="text-medical-blue hover:underline">سياسة الخصوصية</a>
                        </p>
                    </x-ui.card-content>
                </x-ui.card>
            </div>
        </div>
    </section>

    {{-- ===== Demo Dashboard ===== --}}
    <div x-show="loggedIn" x-cloak class="bg-gray-50 dark:bg-gray-900">
        <section class="pt-32 pb-16">
            <div class="container-custom">
                {{-- Welcome --}}
                <div class="bg-gradient-to-r from-medical-blue to-medical-blue-dark rounded-2xl p-8 text-white mb-8">
                    <div class="flex items-center gap-4">
                        <div class="w-16 h-16 rounded-full bg-white/20 flex items-center justify-center text-2xl font-bold">س</div>
                        <div>
                            <h1 class="text-2xl font-bold">مرحباً سارة</h1>
                            <p class="text-white/80">آخر زيارة: 15 يناير 2024</p>
                        </div>
                    </div>
                </div>

                {{-- Quick Actions --}}
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
                    @foreach ([
                        ['label' => 'حجز موعد', 'icon' => 'calendar', 'href' => '/booking', 'color' => 'bg-blue-500'],
                        ['label' => 'السجل الطبي', 'icon' => 'file-text', 'href' => '#records', 'color' => 'bg-green-500'],
                        ['label' => 'متابعة الحمل', 'icon' => 'baby', 'href' => '#pregnancy', 'color' => 'bg-pink-500'],
                        ['label' => 'رسالة للطبيب', 'icon' => 'message-square', 'href' => '#messages', 'color' => 'bg-purple-500'],
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
                                <h2 class="text-lg font-bold text-gray-800 dark:text-white">المواعيد القادمة</h2>
                                <a href="/booking" class="text-medical-blue text-sm hover:underline">حجز جديد</a>
                            </x-ui.card-header>
                            <x-ui.card-content>
                                <div class="space-y-4">
                                    @foreach ($appointments as $appointment)
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
                                    @endforeach
                                </div>
                            </x-ui.card-content>
                        </x-ui.card>

                        {{-- Pregnancy Tracking --}}
                        <x-ui.card>
                            <x-ui.card-header class="flex justify-between items-center">
                                <h2 class="text-lg font-bold text-gray-800 dark:text-white">متابعة الحمل</h2>
                                <span class="px-3 py-1 bg-pink-100 text-pink-700 rounded-full text-sm">الأسبوع 24</span>
                            </x-ui.card-header>
                            <x-ui.card-content>
                                <div class="text-center mb-6">
                                    <div class="w-32 h-32 mx-auto mb-4 rounded-full bg-pink-100 flex items-center justify-center">
                                        @svg('lucide-baby', 'w-16 h-16 text-pink-500')
                                    </div>
                                    <h3 class="text-xl font-bold text-gray-800 dark:text-white">طفلك الآن بحجم ثمرة الذرة!</h3>
                                    <p class="text-gray-500">الوزن التقريبي: 600 جرام</p>
                                </div>
                                <div class="grid grid-cols-3 gap-4 mb-6">
                                    <div class="text-center p-3 bg-gray-50 dark:bg-gray-700 rounded-xl">
                                        <p class="text-2xl font-bold text-medical-blue">24</p>
                                        <p class="text-xs text-gray-500">أسبوع</p>
                                    </div>
                                    <div class="text-center p-3 bg-gray-50 dark:bg-gray-700 rounded-xl">
                                        <p class="text-2xl font-bold text-pink-500">112</p>
                                        <p class="text-xs text-gray-500">يوم متبقي</p>
                                    </div>
                                    <div class="text-center p-3 bg-gray-50 dark:bg-gray-700 rounded-xl">
                                        <p class="text-2xl font-bold text-green-500">60%</p>
                                        <p class="text-xs text-gray-500">اكتمل</p>
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
                                <h2 class="text-lg font-bold text-gray-800 dark:text-white">السجل الطبي</h2>
                            </x-ui.card-header>
                            <x-ui.card-content>
                                <div class="space-y-3">
                                    @foreach ($records as $record)
                                        <div class="flex items-center justify-between p-3 border border-gray-200 dark:border-gray-700 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-800 cursor-pointer">
                                            <div class="flex items-center gap-3">
                                                @svg('lucide-file-text', 'w-5 h-5 text-medical-blue')
                                                <div>
                                                    <p class="font-medium text-gray-800 dark:text-white">{{ $record['name'] }}</p>
                                                    <p class="text-xs text-gray-500">{{ $record['type'] }} - {{ $record['date'] }}</p>
                                                </div>
                                            </div>
                                            <button class="text-medical-blue text-sm hover:underline">عرض</button>
                                        </div>
                                    @endforeach
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
                                    <div class="w-20 h-20 mx-auto mb-4 rounded-full bg-medical-blue/10 flex items-center justify-center text-3xl font-bold text-medical-blue">س</div>
                                    <h3 class="font-bold text-gray-800 dark:text-white">سارة أحمد</h3>
                                    <p class="text-sm text-gray-500 mb-4">رقم الملف: P2024001</p>
                                    <div class="text-left space-y-2 text-sm">
                                        <p class="text-gray-600 dark:text-gray-400">📱 01012345678</p>
                                        <p class="text-gray-600 dark:text-gray-400">✉️ sara@email.com</p>
                                    </div>
                                </div>
                            </x-ui.card-content>
                        </x-ui.card>

                        {{-- Notifications --}}
                        <x-ui.card>
                            <x-ui.card-header>
                                <h3 class="font-bold text-gray-800 dark:text-white flex items-center gap-2">
                                    @svg('lucide-bell', 'w-[18px] h-[18px]')
                                    الإشعارات
                                </h3>
                            </x-ui.card-header>
                            <x-ui.card-content>
                                <div class="space-y-3">
                                    <div class="p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                                        <p class="text-sm text-gray-700 dark:text-gray-300">تذكير: موعدك غداً الساعة 10 صباحاً</p>
                                        <p class="text-xs text-gray-500 mt-1">منذ ساعة</p>
                                    </div>
                                    <div class="p-3 bg-green-50 dark:bg-green-900/20 rounded-lg">
                                        <p class="text-sm text-gray-700 dark:text-gray-300">نتائج تحاليلك جاهزة للمراجعة</p>
                                        <p class="text-xs text-gray-500 mt-1">منذ 3 ساعات</p>
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
                                        <span class="text-gray-700 dark:text-gray-300">إعدادات الحساب</span>
                                    </a>
                                    <a href="#" class="flex items-center gap-3 p-3 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg">
                                        @svg('lucide-lock', 'w-[18px] h-[18px] text-gray-500')
                                        <span class="text-gray-700 dark:text-gray-300">تغيير كلمة المرور</span>
                                    </a>
                                    <button @click="loggedIn = false"
                                            class="w-full flex items-center gap-3 p-3 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg text-red-500">
                                        @svg('lucide-log-in', 'w-[18px] h-[18px]')
                                        <span>تسجيل الخروج</span>
                                    </button>
                                </div>
                            </x-ui.card-content>
                        </x-ui.card>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>
@endsection
