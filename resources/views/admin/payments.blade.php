@extends('layouts.admin')

@php
    $statusColors = ['paid' => 'green', 'pending' => 'yellow', 'refunded' => 'blue', 'failed' => 'red'];
    $methodOptions = [
        ['value' => 'نقدي', 'label' => 'نقدي'],
        ['value' => 'فيزا', 'label' => 'فيزا'],
        ['value' => 'فودافون كاش', 'label' => 'فودافون كاش'],
        ['value' => 'انستاباي', 'label' => 'انستاباي'],
    ];
    $invoiceStatusOptions = [
        ['value' => 'pending', 'label' => __('pending')],
        ['value' => 'paid', 'label' => __('paid')],
    ];
@endphp

@section('content')
<div x-data="{ createOpen: false, viewOpen: false, current: {} }">
    <x-admin.page-header :title="__('payments')">
        <x-slot:actions>
            <x-ui.button variant="primary" size="sm" x-on:click="createOpen = true">
                <x-slot:leftIcon>@svg('lucide-plus', 'w-[18px] h-[18px]')</x-slot:leftIcon>
                {{ __('issue_invoice') }}
            </x-ui.button>
        </x-slot:actions>
    </x-admin.page-header>

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        @foreach ($stats as $s)
            <x-admin.stat-tile :label="$s['label']" :value="$s['value']" :icon="$s['icon']" :color="$s['color']" />
        @endforeach
    </div>

    <x-ui.card>
        <x-ui.card-content class="p-0">
            <div class="overflow-x-auto">
                <table class="w-full text-start">
                    <thead class="bg-gray-50 dark:bg-gray-700/50">
                        <tr class="text-sm font-semibold text-gray-600 dark:text-gray-300">
                            <th class="px-6 py-4 text-start">{{ __('invoice_number') }}</th>
                            <th class="px-6 py-4 text-start">{{ __('patient_name') }}</th>
                            <th class="px-6 py-4 text-start">{{ __('serviceLabel') }}</th>
                            <th class="px-6 py-4 text-start">{{ __('cost') }}</th>
                            <th class="px-6 py-4 text-start">{{ __('status') }}</th>
                            <th class="px-6 py-4 text-start">{{ __('date') }}</th>
                            <th class="px-6 py-4 text-start">{{ __('actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse ($invoices as $inv)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                                <td class="px-6 py-4 font-mono text-sm text-gray-800 dark:text-gray-200">{{ $inv['number'] }}</td>
                                <td class="px-6 py-4 text-gray-800 dark:text-gray-200">{{ $inv['patient'] }}</td>
                                <td class="px-6 py-4 text-gray-600 dark:text-gray-400">{{ $inv['service'] }}</td>
                                <td class="px-6 py-4 font-medium text-medical-blue">{{ number_format($inv['amount']) }} {{ __('egp') }}</td>
                                <td class="px-6 py-4">
                                    <x-admin.badge :color="$statusColors[$inv['status']] ?? 'gray'">{{ __($inv['status']) }}</x-admin.badge>
                                </td>
                                <td class="px-6 py-4 text-gray-600 dark:text-gray-400">{{ $inv['date'] }}</td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-1">
                                        <button class="p-1.5 text-gray-400 hover:text-medical-blue" title="{{ __('view') }}" x-on:click="current = {{ Js::from($inv) }}; viewOpen = true">@svg('lucide-eye', 'w-[18px] h-[18px]')</button>
                                        @if ($inv['status'] !== 'paid')
                                            <form method="POST" action="{{ route('admin.invoices.paid', $inv['id']) }}">
                                                @csrf @method('PATCH')
                                                <button type="submit" class="p-1.5 text-gray-400 hover:text-green-500" title="{{ __('markPaid') }}">@svg('lucide-check-circle', 'w-[18px] h-[18px]')</button>
                                            </form>
                                        @endif
                                        <form method="POST" action="{{ route('admin.invoices.destroy', $inv['id']) }}" onsubmit="return confirm('{{ __('confirmDelete') }}')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="p-1.5 text-gray-400 hover:text-red-500">@svg('lucide-trash-2', 'w-[18px] h-[18px]')</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="px-6 py-10 text-center text-gray-400">{{ __('noRecords') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-ui.card-content>
    </x-ui.card>

    {{-- Create invoice --}}
    <x-admin.modal :title="__('issue_invoice')" var="createOpen">
        <form method="POST" action="{{ route('admin.invoices.store') }}" class="grid md:grid-cols-2 gap-4">
            @csrf
            <div class="md:col-span-2">
                <x-ui.select :label="__('patient_name')" name="patient_id" :options="$patientOptions" :placeholder="__('selectPatient')" required />
            </div>
            <div class="md:col-span-2">
                <x-ui.input :label="__('serviceLabel')" name="service" required />
            </div>
            <x-ui.input :label="__('amount')" name="amount" type="number" min="0" required />
            <x-ui.select :label="__('payment_method')" name="payment_method" :options="$methodOptions" />
            <x-ui.input :label="__('date')" name="date" type="date" value="{{ now()->toDateString() }}" />
            <x-ui.select :label="__('status')" name="status" :options="$invoiceStatusOptions" />
            <div class="md:col-span-2 flex justify-end gap-3 pt-4 border-t border-gray-100 dark:border-gray-700">
                <x-ui.button type="button" variant="outline" size="sm" x-on:click="createOpen = false">{{ __('cancel') }}</x-ui.button>
                <x-ui.button type="submit" variant="primary" size="sm">{{ __('save') }}</x-ui.button>
            </div>
        </form>
    </x-admin.modal>

    {{-- Printable invoice --}}
    <x-admin.modal :title="__('invoice_number')" var="viewOpen">
        <div class="border border-gray-200 dark:border-gray-700 rounded-xl p-6" id="invoice-print">
            <div class="flex items-center justify-between mb-6 pb-4 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 overflow-hidden flex items-center justify-center"><img src="{{ asset('images/logo.png') }}" alt="{{ __('heroTitle') }}" class="w-full h-full object-contain"></div>
                    <div>
                        <p class="font-bold text-gray-800 dark:text-white">{{ __('heroTitle') }}</p>
                        <p class="text-xs text-gray-500">{{ __('doctorTitleShort') }}</p>
                    </div>
                </div>
                <p class="font-mono text-sm text-gray-500" x-text="current.number"></p>
            </div>
            <p class="text-sm text-gray-500 mb-1">{{ __('patient_name') }}</p>
            <p class="font-medium text-gray-800 dark:text-white mb-4" x-text="current.patient"></p>
            <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                <span class="text-gray-600 dark:text-gray-300" x-text="current.service"></span>
                <span class="text-xl font-bold text-medical-blue"><span x-text="current.amount"></span> {{ __('egp') }}</span>
            </div>
        </div>
        <x-slot:footer>
            <x-ui.button variant="outline" size="sm" x-on:click="window.print()">
                <x-slot:leftIcon>@svg('lucide-printer', 'w-[18px] h-[18px]')</x-slot:leftIcon>
                {{ __('view') }}
            </x-ui.button>
        </x-slot:footer>
    </x-admin.modal>
</div>
@endsection
