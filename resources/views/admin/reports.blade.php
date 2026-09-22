@extends('layouts.admin')

@php
    $max = max($monthly) ?: 1;
    // Build a conic-gradient for the donut from the distribution percentages.
    $hex = ['bg-medical-blue' => '#1e5f9f', 'bg-light-gold' => '#d4af37', 'bg-pink-500' => '#ec4899', 'bg-emerald-500' => '#10b981'];
    $stops = [];
    $acc = 0;
    foreach ($distribution as $d) {
        $c = $hex[$d['color']] ?? '#9ca3af';
        $stops[] = "$c {$acc}% " . ($acc + $d['percent']) . '%';
        $acc += $d['percent'];
    }
    $conic = 'conic-gradient(' . implode(', ', $stops) . ')';
@endphp

@section('content')
<div>
    <x-admin.page-header :title="__('reports')">
        <x-slot:actions>
            <x-ui.button variant="gold" size="sm">
                <x-slot:leftIcon>@svg('lucide-sparkles', 'w-[18px] h-[18px]')</x-slot:leftIcon>
                {{ __('ai_insights') }}
            </x-ui.button>
        </x-slot:actions>
    </x-admin.page-header>

    {{-- Export detailed reports (Excel/CSV) with an optional date range --}}
    <x-ui.card class="mb-6">
        <x-ui.card-header>
            <div class="flex items-center gap-2">
                @svg('lucide-download', 'w-5 h-5 text-medical-blue')
                <h2 class="font-bold text-gray-800 dark:text-white">تصدير التقارير التفصيلية (Excel)</h2>
            </div>
        </x-ui.card-header>
        <x-ui.card-content>
            <form method="GET" action="{{ url('/admin/reports/export') }}" class="space-y-4">
                <div class="flex flex-col md:flex-row gap-4">
                    <div class="flex-1">
                        <label class="block text-sm text-gray-500 mb-1">من تاريخ</label>
                        <input type="date" name="from" class="w-full px-4 py-2.5 border rounded-lg bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-medical-blue">
                    </div>
                    <div class="flex-1">
                        <label class="block text-sm text-gray-500 mb-1">إلى تاريخ</label>
                        <input type="date" name="to" class="w-full px-4 py-2.5 border rounded-lg bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-medical-blue">
                    </div>
                </div>
                <p class="text-xs text-gray-400">اتركي التاريخ فارغًا لتصدير كل السجلات. كل ملف يحتوي على التفاصيل الكاملة (بما في ذلك التاريخ والوقت).</p>
                <div class="flex flex-wrap gap-3">
                    <button type="submit" name="type" value="patients" class="inline-flex items-center gap-2 bg-medical-blue text-white px-5 py-2.5 rounded-lg text-sm font-medium hover:bg-medical-blue-dark transition-colors">
                        @svg('lucide-users', 'w-4 h-4') تصدير المريضات
                    </button>
                    <button type="submit" name="type" value="staff" class="inline-flex items-center gap-2 bg-purple-600 text-white px-5 py-2.5 rounded-lg text-sm font-medium hover:bg-purple-700 transition-colors">
                        @svg('lucide-user-cog', 'w-4 h-4') تصدير الفريق الطبي
                    </button>
                    <button type="submit" name="type" value="surgeries" class="inline-flex items-center gap-2 bg-emerald-600 text-white px-5 py-2.5 rounded-lg text-sm font-medium hover:bg-emerald-700 transition-colors">
                        @svg('lucide-activity', 'w-4 h-4') تصدير العمليات
                    </button>
                    <button type="submit" name="type" value="payments" class="inline-flex items-center gap-2 bg-amber-600 text-white px-5 py-2.5 rounded-lg text-sm font-medium hover:bg-amber-700 transition-colors">
                        @svg('lucide-dollar-sign', 'w-4 h-4') تصدير المدفوعات
                    </button>
                    <button type="submit" name="type" value="appointments" class="inline-flex items-center gap-2 bg-cyan-600 text-white px-5 py-2.5 rounded-lg text-sm font-medium hover:bg-cyan-700 transition-colors">
                        @svg('lucide-calendar-check', 'w-4 h-4') تصدير الكشوفات
                    </button>
                    <button type="submit" name="type" value="ivf" class="inline-flex items-center gap-2 bg-pink-600 text-white px-5 py-2.5 rounded-lg text-sm font-medium hover:bg-pink-700 transition-colors">
                        @svg('lucide-baby', 'w-4 h-4') تصدير الحقن المجهري
                    </button>
                    <button type="submit" name="type" value="patients_check" class="inline-flex items-center gap-2 bg-teal-600 text-white px-5 py-2.5 rounded-lg text-sm font-medium hover:bg-teal-700 transition-colors">
                        @svg('lucide-user-check', 'w-4 h-4') مريضات الكشف
                    </button>
                    <button type="submit" name="type" value="patients_surgery" class="inline-flex items-center gap-2 bg-rose-600 text-white px-5 py-2.5 rounded-lg text-sm font-medium hover:bg-rose-700 transition-colors">
                        @svg('lucide-scissors', 'w-4 h-4') مريضات العمليات
                    </button>
                </div>
            </form>
        </x-ui.card-content>
    </x-ui.card>

    {{-- KPIs --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        @foreach ($kpis as $k)
            <x-admin.stat-tile :label="$k['label']" :value="$k['value']" :icon="$k['icon']" :color="$k['color']" />
        @endforeach
    </div>

    <div class="grid lg:grid-cols-3 gap-6 mb-6">
        {{-- Bar chart --}}
        <x-ui.card class="lg:col-span-2">
            <x-ui.card-header>
                <h2 class="font-bold text-gray-800 dark:text-white">{{ __('activity_analysis') }}</h2>
            </x-ui.card-header>
            <x-ui.card-content>
                <div class="flex items-end justify-between gap-2 h-48">
                    @foreach ($monthly as $v)
                        <div class="flex-1 flex flex-col items-center justify-end h-full group">
                            <div class="w-full bg-medical-blue/15 rounded-t-md relative flex items-end" style="height: 100%">
                                <div class="w-full bg-medical-blue rounded-t-md transition-all group-hover:bg-medical-blue-dark" style="height: {{ round($v / $max * 100) }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </x-ui.card-content>
        </x-ui.card>

        {{-- Donut --}}
        <x-ui.card>
            <x-ui.card-header>
                <h2 class="font-bold text-gray-800 dark:text-white">{{ __('services') }}</h2>
            </x-ui.card-header>
            <x-ui.card-content>
                <div class="flex justify-center mb-4">
                    <div class="relative w-40 h-40 rounded-full" style="background: {{ $conic }}">
                        <div class="absolute inset-6 bg-white dark:bg-gray-800 rounded-full flex items-center justify-center">
                            <span class="text-sm font-bold text-gray-800 dark:text-white">100%</span>
                        </div>
                    </div>
                </div>
                <div class="space-y-2">
                    @foreach ($distribution as $d)
                        <div class="flex items-center justify-between text-sm">
                            <div class="flex items-center gap-2">
                                <span class="w-3 h-3 rounded-full {{ $d['color'] }}"></span>
                                <span class="text-gray-600 dark:text-gray-400">{{ $d['label'] }}</span>
                            </div>
                            <span class="font-medium text-gray-800 dark:text-white">{{ $d['percent'] }}%</span>
                        </div>
                    @endforeach
                </div>
            </x-ui.card-content>
        </x-ui.card>
    </div>

    {{-- Patients by type (كشف / عمليات / حقن مجهري) --}}
    <x-ui.card class="mb-6">
        <x-ui.card-header>
            <div class="flex items-center gap-2">
                @svg('lucide-users', 'w-5 h-5 text-medical-blue')
                <h2 class="font-bold text-gray-800 dark:text-white">تقرير المريضات حسب النوع</h2>
            </div>
        </x-ui.card-header>
        <x-ui.card-content>
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                @foreach ($patientTypeReport as $r)
                    <div class="p-4 rounded-xl bg-gray-50 dark:bg-gray-800">
                        <p class="text-sm text-gray-500">{{ $r['label'] }}</p>
                        <p class="text-xl font-bold text-gray-800 dark:text-white mt-1">{{ $r['value'] }}</p>
                    </div>
                @endforeach
            </div>
        </x-ui.card-content>
    </x-ui.card>

    {{-- Consultations (الكشف) + IVF (الحقن المجهري) case reports --}}
    <div class="grid lg:grid-cols-2 gap-6 mb-6">
        <x-ui.card>
            <x-ui.card-header>
                <div class="flex items-center gap-2">
                    @svg('lucide-calendar-check', 'w-5 h-5 text-medical-blue')
                    <h2 class="font-bold text-gray-800 dark:text-white">تقرير الكشف (حالات المواعيد)</h2>
                </div>
            </x-ui.card-header>
            <x-ui.card-content>
                <div class="space-y-3">
                    @foreach ($appointmentReport as $r)
                        <div>
                            <div class="flex justify-between text-sm mb-1">
                                <span class="text-gray-600 dark:text-gray-400">{{ $r['label'] }}</span>
                                <span class="font-medium text-gray-800 dark:text-white">{{ $r['count'] }}</span>
                            </div>
                            <div class="h-2 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                                <div class="h-full bg-medical-blue rounded-full transition-all" style="width: {{ $r['percent'] }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </x-ui.card-content>
        </x-ui.card>

        <x-ui.card>
            <x-ui.card-header>
                <div class="flex items-center gap-2">
                    @svg('lucide-baby', 'w-5 h-5 text-medical-blue')
                    <h2 class="font-bold text-gray-800 dark:text-white">تقرير الحقن المجهري</h2>
                </div>
            </x-ui.card-header>
            <x-ui.card-content>
                <div class="grid grid-cols-2 gap-3 mb-4">
                    @foreach ($ivfReport['kpis'] as $k)
                        <div class="p-3 rounded-lg bg-gray-50 dark:bg-gray-800">
                            <p class="text-xs text-gray-500">{{ $k['label'] }}</p>
                            <p class="text-lg font-bold text-gray-800 dark:text-white mt-1">{{ $k['value'] }}</p>
                        </div>
                    @endforeach
                </div>
                <div class="space-y-3">
                    @foreach ($ivfReport['stages'] as $s)
                        <div>
                            <div class="flex justify-between text-sm mb-1">
                                <span class="text-gray-600 dark:text-gray-400">{{ $s['label'] }}</span>
                                <span class="font-medium text-gray-800 dark:text-white">{{ $s['count'] }}</span>
                            </div>
                            <div class="h-2 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                                <div class="h-full bg-pink-500 rounded-full transition-all" style="width: {{ $s['percent'] }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </x-ui.card-content>
        </x-ui.card>
    </div>

    {{-- Surgeries report --}}
    <x-ui.card class="mb-6">
        <x-ui.card-header>
            <div class="flex items-center gap-2">
                @svg('lucide-activity', 'w-5 h-5 text-medical-blue')
                <h2 class="font-bold text-gray-800 dark:text-white">تقرير العمليات</h2>
            </div>
        </x-ui.card-header>
        <x-ui.card-content>
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                @foreach ($surgeryReport['kpis'] as $k)
                    <div class="p-4 rounded-xl bg-gray-50 dark:bg-gray-800">
                        <p class="text-sm text-gray-500">{{ $k['label'] }}</p>
                        <p class="text-xl font-bold text-gray-800 dark:text-white mt-1">{{ $k['value'] }}</p>
                    </div>
                @endforeach
            </div>
            <div class="space-y-3">
                @foreach ($surgeryReport['types'] as $t)
                    <div>
                        <div class="flex justify-between text-sm mb-1">
                            <span class="text-gray-600 dark:text-gray-400">{{ $t['label'] }}</span>
                            <span class="font-medium text-gray-800 dark:text-white">{{ $t['count'] }}</span>
                        </div>
                        <div class="h-2 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                            <div class="h-full bg-medical-blue rounded-full transition-all" style="width: {{ $t['percent'] }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </x-ui.card-content>
    </x-ui.card>

    {{-- Staff + Payments reports --}}
    <div class="grid lg:grid-cols-2 gap-6 mb-6">
        <x-ui.card>
            <x-ui.card-header>
                <div class="flex items-center gap-2">
                    @svg('lucide-users', 'w-5 h-5 text-medical-blue')
                    <h2 class="font-bold text-gray-800 dark:text-white">تقرير الفريق الطبي</h2>
                </div>
            </x-ui.card-header>
            <x-ui.card-content>
                <div class="space-y-3">
                    @foreach ($staffReport as $r)
                        <div class="flex items-center justify-between p-3 rounded-lg bg-gray-50 dark:bg-gray-800">
                            <span class="text-gray-600 dark:text-gray-400">{{ $r['label'] }}</span>
                            <span class="font-bold text-gray-800 dark:text-white">{{ $r['count'] }}</span>
                        </div>
                    @endforeach
                </div>
            </x-ui.card-content>
        </x-ui.card>

        <x-ui.card>
            <x-ui.card-header>
                <div class="flex items-center gap-2">
                    @svg('lucide-dollar-sign', 'w-5 h-5 text-medical-blue')
                    <h2 class="font-bold text-gray-800 dark:text-white">تقرير المدفوعات</h2>
                </div>
            </x-ui.card-header>
            <x-ui.card-content>
                <div class="grid grid-cols-2 gap-3 mb-4">
                    @foreach ($paymentReport['kpis'] as $k)
                        <div class="p-3 rounded-lg bg-gray-50 dark:bg-gray-800">
                            <p class="text-xs text-gray-500">{{ $k['label'] }}</p>
                            <p class="text-lg font-bold text-gray-800 dark:text-white mt-1">{{ $k['value'] }}</p>
                        </div>
                    @endforeach
                </div>
                <div class="space-y-2">
                    @forelse ($paymentReport['methods'] as $m)
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-600 dark:text-gray-400">{{ $m['label'] }}</span>
                            <span class="font-medium text-gray-800 dark:text-white">{{ number_format($m['amount']) }} ج.م</span>
                        </div>
                    @empty
                        <p class="text-sm text-gray-400">{{ __('noRecords') }}</p>
                    @endforelse
                </div>
            </x-ui.card-content>
        </x-ui.card>
    </div>

    {{-- AI insights --}}
    <x-ui.card class="bg-gradient-to-r from-medical-blue to-medical-blue-dark text-white">
        <x-ui.card-content class="p-6">
            <div class="flex items-center gap-3 mb-4">
                @svg('lucide-brain-circuit', 'w-6 h-6 text-light-gold')
                <h2 class="font-bold text-lg">{{ __('ai_recommendation') }}</h2>
            </div>
            <div class="grid md:grid-cols-2 gap-4">
                <div class="bg-white/10 rounded-xl p-4">
                    <p class="font-semibold mb-1">{{ __('peak_prediction') }}</p>
                    <p class="text-sm text-white/80">{{ app()->getLocale() === 'en' ? 'Appointment demand peaks on Sundays and Mondays; consider adding staff on those days.' : 'يبلغ الطلب على المواعيد ذروته أيام الأحد والاثنين؛ يُنصح بزيادة الطاقم في هذين اليومين.' }}</p>
                </div>
                <div class="bg-white/10 rounded-xl p-4">
                    <p class="font-semibold mb-1">{{ __('success_rate') }}</p>
                    <p class="text-sm text-white/80">{{ app()->getLocale() === 'en' ? 'IVF success rate improved 5% this quarter compared to the previous one.' : 'تحسّنت نسبة نجاح الحقن المجهري بمقدار 5% هذا الربع مقارنة بالربع السابق.' }}</p>
                </div>
            </div>
        </x-ui.card-content>
    </x-ui.card>
</div>
@endsection
