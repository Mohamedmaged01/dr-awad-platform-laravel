@extends('layouts.admin')

@section('content')
    <div class="space-y-6" x-data="{ search: '', showAddModal: false, showEditModal: false, current: {}, matches(t) { return this.search === '' || t.includes(this.search) } }">
        {{-- Header --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-800 dark:text-white">إدارة المريضات</h1>
                <p class="text-gray-500">إجمالي {{ $patients->count() }} مريضة</p>
            </div>
            <div class="flex gap-3">
                <x-ui.button variant="outline">
                    <x-slot:leftIcon>@svg('lucide-download', 'w-[18px] h-[18px]')</x-slot:leftIcon>
                    تصدير Excel
                </x-ui.button>
                <x-ui.button variant="primary" x-on:click="showAddModal = true">
                    <x-slot:leftIcon>@svg('lucide-plus', 'w-[18px] h-[18px]')</x-slot:leftIcon>
                    إضافة مريضة
                </x-ui.button>
            </div>
        </div>

        {{-- Filters --}}
        <x-ui.card>
            <x-ui.card-content class="p-4">
                <div class="flex flex-col md:flex-row gap-4">
                    <div class="flex-1">
                        <x-ui.input placeholder="بحث بالاسم أو رقم الملف أو الهاتف..." x-model="search">
                            <x-slot:leftIcon>@svg('lucide-search', 'w-[18px] h-[18px]')</x-slot:leftIcon>
                        </x-ui.input>
                    </div>
                    <div class="flex gap-3">
                        <x-ui.button variant="outline">
                            <x-slot:leftIcon>@svg('lucide-filter', 'w-[18px] h-[18px]')</x-slot:leftIcon>
                            تصفية
                        </x-ui.button>
                    </div>
                </div>
            </x-ui.card-content>
        </x-ui.card>

        {{-- Table --}}
        <x-ui.card>
            <x-ui.card-content class="p-0">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                @foreach (['رقم الملف', 'الاسم', 'الهاتف', 'العمر', 'نوع الحالة', 'آخر زيارة', 'الحالة', 'إجراءات'] as $th)
                                    <th class="px-6 py-4 text-right text-sm font-semibold text-gray-600 dark:text-gray-300">{{ $th }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach ($patients as $patient)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800"
                                    x-show="matches(@js($patient->name . ' ' . $patient->file_number . ' ' . $patient->phone))">
                                    <td class="px-6 py-4">
                                        <span class="font-mono text-medical-blue">{{ $patient->file_number }}</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-full bg-medical-blue/10 flex items-center justify-center text-medical-blue font-bold">
                                                {{ mb_substr($patient->name, 0, 1) }}
                                            </div>
                                            <div>
                                                <p class="font-medium text-gray-800 dark:text-white">{{ $patient->name }}</p>
                                                <p class="text-sm text-gray-500">{{ $patient->email }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <a href="tel:{{ $patient->phone }}" class="text-gray-600 dark:text-gray-300 hover:text-medical-blue">{{ $patient->phone }}</a>
                                    </td>
                                    <td class="px-6 py-4 text-gray-600 dark:text-gray-300">{{ $patient->age }} سنة</td>
                                    <td class="px-6 py-4">
                                        <span class="px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-300">
                                            {{ $patient->case_type }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-gray-600 dark:text-gray-300">
                                        {{ \Carbon\Carbon::parse($patient->last_visit)->locale('ar_EG')->isoFormat('D/M/YYYY') }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <span @class([
                                            'px-3 py-1 rounded-full text-xs font-medium',
                                            'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300' => $patient->demo_status === 'active',
                                            'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300' => $patient->demo_status !== 'active',
                                        ])>
                                            {{ $patient->demo_status === 'active' ? 'نشط' : 'مؤرشف' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-2">
                                            <button class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg" title="تعديل"
                                                    x-on:click="current = {{ Js::from([
                                                        'id' => $patient->id,
                                                        'first_name_ar' => $patient->first_name_ar,
                                                        'last_name_ar' => $patient->last_name_ar,
                                                        'phone' => $patient->phone,
                                                        'phone_alt' => $patient->phone_alt,
                                                        'email' => $patient->email,
                                                        'address' => $patient->address,
                                                        'case_type' => $patient->case_type,
                                                        'demo_status' => $patient->demo_status,
                                                    ]) }}; showEditModal = true">
                                                @svg('lucide-edit', 'w-[18px] h-[18px] text-gray-500')
                                            </button>
                                            <form method="POST" action="{{ route('admin.patients.destroy', $patient->id) }}"
                                                  onsubmit="return confirm('{{ __('confirmDelete') }}')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg" title="حذف">
                                                    @svg('lucide-trash-2', 'w-[18px] h-[18px] text-red-500')
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 flex items-center justify-between">
                    <p class="text-sm text-gray-500">عرض 1-{{ $patients->count() }} من {{ $patients->count() }}</p>
                    <div class="flex gap-2">
                        <button class="px-4 py-2 border border-gray-200 dark:border-gray-700 rounded-lg text-sm hover:bg-gray-50 dark:hover:bg-gray-800">السابق</button>
                        <button class="px-4 py-2 bg-medical-blue text-white rounded-lg text-sm">1</button>
                        <button class="px-4 py-2 border border-gray-200 dark:border-gray-700 rounded-lg text-sm hover:bg-gray-50 dark:hover:bg-gray-800">2</button>
                        <button class="px-4 py-2 border border-gray-200 dark:border-gray-700 rounded-lg text-sm hover:bg-gray-50 dark:hover:bg-gray-800">التالي</button>
                    </div>
                </div>
            </x-ui.card-content>
        </x-ui.card>

        {{-- Add Modal --}}
        <div x-show="showAddModal" x-cloak class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
            <x-ui.card class="w-full max-w-2xl max-h-[90vh] overflow-y-auto">
                <x-ui.card-header class="flex justify-between items-center">
                    <h2 class="text-xl font-bold text-gray-800 dark:text-white">إضافة مريضة جديدة</h2>
                    <button @click="showAddModal = false" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg">✕</button>
                </x-ui.card-header>
                <x-ui.card-content>
                    <form class="space-y-4" method="POST" action="{{ route('admin.patients.store') }}">
                        @csrf
                        <div class="grid md:grid-cols-2 gap-4">
                            <x-ui.input label="الاسم الأول (عربي)" name="first_name_ar" required />
                            <x-ui.input label="اسم العائلة (عربي)" name="last_name_ar" required />
                        </div>
                        <div class="grid md:grid-cols-2 gap-4">
                            <x-ui.input label="رقم الهاتف" name="phone" type="tel" required />
                            <x-ui.input label="هاتف بديل" name="phone_alt" type="tel" />
                        </div>
                        <x-ui.input label="البريد الإلكتروني" name="email" type="email" />
                        <div class="grid md:grid-cols-2 gap-4">
                            <x-ui.input label="تاريخ الميلاد" name="date_of_birth" type="date" />
                            <x-ui.input label="الرقم القومي" name="national_id" />
                        </div>
                        <x-ui.input label="العنوان" name="address" />
                        <div class="grid md:grid-cols-2 gap-4">
                            <x-ui.input label="نوع الحالة" name="case_type" />
                            <x-ui.input label="جهة الطوارئ" name="emergency_contact" />
                        </div>
                        <div class="flex justify-end gap-3 pt-4">
                            <x-ui.button type="button" variant="outline" x-on:click="showAddModal = false">إلغاء</x-ui.button>
                            <x-ui.button type="submit" variant="primary">حفظ المريضة</x-ui.button>
                        </div>
                    </form>
                </x-ui.card-content>
            </x-ui.card>
        </div>

        {{-- Edit Modal --}}
        <div x-show="showEditModal" x-cloak class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
            <x-ui.card class="w-full max-w-2xl max-h-[90vh] overflow-y-auto">
                <x-ui.card-header class="flex justify-between items-center">
                    <h2 class="text-xl font-bold text-gray-800 dark:text-white">تعديل بيانات المريضة</h2>
                    <button @click="showEditModal = false" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg">✕</button>
                </x-ui.card-header>
                <x-ui.card-content>
                    <form class="space-y-4" method="POST" :action="'{{ url('/admin/patients') }}/' + current.id">
                        @csrf @method('PUT')
                        <div class="grid md:grid-cols-2 gap-4">
                            <x-ui.input label="الاسم الأول (عربي)" name="first_name_ar" x-model="current.first_name_ar" required />
                            <x-ui.input label="اسم العائلة (عربي)" name="last_name_ar" x-model="current.last_name_ar" required />
                        </div>
                        <div class="grid md:grid-cols-2 gap-4">
                            <x-ui.input label="رقم الهاتف" name="phone" type="tel" x-model="current.phone" required />
                            <x-ui.input label="هاتف بديل" name="phone_alt" type="tel" x-model="current.phone_alt" />
                        </div>
                        <x-ui.input label="البريد الإلكتروني" name="email" type="email" x-model="current.email" />
                        <x-ui.input label="العنوان" name="address" x-model="current.address" />
                        <div class="grid md:grid-cols-2 gap-4">
                            <x-ui.input label="نوع الحالة" name="case_type" x-model="current.case_type" />
                            <x-ui.select label="الحالة" name="demo_status" x-model="current.demo_status" :options="[
                                ['value' => 'active', 'label' => 'نشط'],
                                ['value' => 'archived', 'label' => 'مؤرشف'],
                            ]" />
                        </div>
                        <div class="flex justify-end gap-3 pt-4">
                            <x-ui.button type="button" variant="outline" x-on:click="showEditModal = false">إلغاء</x-ui.button>
                            <x-ui.button type="submit" variant="primary">حفظ التغييرات</x-ui.button>
                        </div>
                    </form>
                </x-ui.card-content>
            </x-ui.card>
        </div>
    </div>
@endsection
