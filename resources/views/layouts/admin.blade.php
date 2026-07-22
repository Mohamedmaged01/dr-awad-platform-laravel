<!DOCTYPE html>
<html lang="ar" dir="rtl" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'لوحة التحكم | د. محمد عوض')</title>

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
@php
    $current = '/' . request()->path();
    $menuTitle = collect(config('clinic.dashboard_menu'))->firstWhere('href', $current)['name'] ?? 'لوحة التحكم';
    $roleLabels = config('clinic.role_labels');
    $staffRoles = config('clinic.staff_roles');
@endphp
<body class="font-cairo antialiased">
<div class="min-h-screen bg-gray-100 dark:bg-gray-900" dir="rtl"
     x-data="{ sidebar: true, role: 'admin', showRoleSwitcher: false, showNotifications: false, roleLabels: @js($roleLabels) }">

    {{-- Sidebar --}}
    <aside :class="sidebar ? 'w-64' : 'w-20'"
           class="fixed top-0 right-0 z-40 h-full bg-white dark:bg-gray-800 shadow-xl transition-all duration-300">
        {{-- Logo --}}
        <div class="h-16 flex items-center justify-between px-4 border-b border-gray-200 dark:border-gray-700">
            <a href="/admin" class="flex items-center gap-3" x-show="sidebar">
                <div class="w-10 h-10 rounded-full gradient-medical flex items-center justify-center text-white font-bold">
                    م.ع
                </div>
                <span class="font-bold text-gray-800 dark:text-white">لوحة التحكم</span>
            </a>
            <button @click="sidebar = !sidebar" class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                <span x-show="sidebar">@svg('lucide-x', 'w-5 h-5')</span>
                <span x-show="!sidebar">@svg('lucide-menu', 'w-5 h-5')</span>
            </button>
        </div>

        {{-- Menu --}}
        <nav class="p-4 space-y-1 overflow-y-auto h-[calc(100vh-8rem)]">
            @foreach (config('clinic.dashboard_menu') as $item)
                @php $isActive = $current === $item['href']; @endphp
                <a href="{{ $item['href'] }}"
                   x-show="{{ Js::from($item['roles']) }}.includes(role)"
                   @class([
                       'flex items-center gap-3 px-4 py-3 rounded-lg transition-all duration-200',
                       'bg-medical-blue text-white' => $isActive,
                       'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' => ! $isActive,
                   ])>
                    @svg('lucide-' . $item['icon'], 'w-5 h-5 flex-shrink-0')
                    <span x-show="sidebar">{{ $item['name'] }}</span>
                </a>
            @endforeach
        </nav>

        {{-- Role Switcher (Demo Only) --}}
        <div class="absolute bottom-0 w-full p-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
            <button @click="showRoleSwitcher = !showRoleSwitcher"
                    class="flex items-center gap-2 w-full px-3 py-2 text-xs font-semibold text-medical-blue bg-white dark:bg-gray-800 border border-medical-blue/20 rounded-lg hover:bg-medical-blue hover:text-white transition-all">
                @svg('lucide-shield-check', 'w-4 h-4')
                <span x-show="sidebar">تبديل الصلاحية (تجربة)</span>
            </button>

            <div x-show="showRoleSwitcher && sidebar" x-cloak
                 class="absolute bottom-16 right-4 left-4 bg-white dark:bg-gray-700 shadow-2xl rounded-xl border border-gray-200 dark:border-gray-600 p-2 z-50">
                @foreach ($staffRoles as $r)
                    <button @click="role = '{{ $r }}'; showRoleSwitcher = false"
                            :class="role === '{{ $r }}' ? 'text-medical-blue font-bold bg-medical-blue/10' : 'text-gray-600 dark:text-gray-300'"
                            class="w-full text-right px-4 py-2 text-sm rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600">
                        {{ $roleLabels[$r] }}
                    </button>
                @endforeach
            </div>
        </div>
    </aside>

    {{-- Main Content --}}
    <div :class="sidebar ? 'mr-64' : 'mr-20'" class="transition-all duration-300">
        {{-- Top Bar --}}
        <header class="h-16 bg-white dark:bg-gray-800 shadow-sm flex items-center justify-between px-6 sticky top-0 z-30">
            <div class="flex items-center gap-4">
                <h1 class="text-xl font-bold text-gray-800 dark:text-white">{{ $menuTitle }}</h1>
            </div>

            <div class="flex items-center gap-4">
                {{-- Theme Toggle --}}
                <button @click="$store.theme.toggle()"
                        class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-600 dark:text-gray-300">
                    <span x-show="!$store.theme.dark">@svg('lucide-moon', 'w-5 h-5')</span>
                    <span x-show="$store.theme.dark" x-cloak>@svg('lucide-sun', 'w-5 h-5')</span>
                </button>

                {{-- Notifications --}}
                <div class="relative">
                    <button @click="showNotifications = !showNotifications"
                            class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-600 dark:text-gray-300 relative">
                        @svg('lucide-bell', 'w-5 h-5')
                        <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"></span>
                    </button>
                    <div x-show="showNotifications" x-cloak @click.outside="showNotifications = false"
                         class="absolute left-0 mt-2 w-80 bg-white dark:bg-gray-800 rounded-xl shadow-xl border border-gray-200 dark:border-gray-700 z-50">
                        <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="font-bold text-gray-800 dark:text-white">الإشعارات</h3>
                        </div>
                        <div class="p-4 space-y-3 max-h-80 overflow-y-auto">
                            <div class="p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                                <p class="text-sm text-gray-700 dark:text-gray-300">حجز جديد من سارة أحمد</p>
                                <p class="text-xs text-gray-500 mt-1">منذ 5 دقائق</p>
                            </div>
                            <div class="p-3 bg-green-50 dark:bg-green-900/20 rounded-lg">
                                <p class="text-sm text-gray-700 dark:text-gray-300">تم تأكيد موعد هالة محمد</p>
                                <p class="text-xs text-gray-500 mt-1">منذ 15 دقيقة</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- User Menu --}}
                <div class="flex items-center gap-3 border-r border-gray-200 dark:border-gray-700 pr-4 mr-4">
                    <div class="w-10 h-10 rounded-full bg-medical-blue text-white flex items-center justify-center font-bold"
                         x-text="role.charAt(0).toUpperCase()"></div>
                    <div class="hidden sm:block">
                        <p class="text-sm font-medium text-gray-800 dark:text-white">مستخدم المنصة</p>
                        <p class="text-xs text-gray-500" x-text="roleLabels[role]"></p>
                    </div>
                </div>

                <a href="/" class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-600 dark:text-gray-300">
                    @svg('lucide-log-out', 'w-5 h-5')
                </a>
            </div>
        </header>

        {{-- Page Content --}}
        <main class="p-6">
            @yield('content')
        </main>
    </div>
</div>
</body>
</html>
