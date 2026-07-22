@extends('layouts.admin')

@section('title', $pageTitle . ' | لوحة التحكم')

@section('content')
    <div class="flex items-center justify-center min-h-[60vh]">
        <x-ui.card class="max-w-lg w-full text-center">
            <x-ui.card-content class="py-16">
                <div class="w-20 h-20 mx-auto mb-6 rounded-full bg-medical-blue/10 flex items-center justify-center">
                    @svg('lucide-hammer', 'w-10 h-10 text-medical-blue')
                </div>
                <h2 class="text-2xl font-bold text-gray-800 dark:text-white mb-3">{{ $pageTitle }}</h2>
                <p class="text-gray-600 dark:text-gray-400 mb-8">هذا القسم قيد التطوير.</p>
                <x-ui.button href="/admin" variant="primary">العودة للوحة التحكم</x-ui.button>
            </x-ui.card-content>
        </x-ui.card>
    </div>
@endsection
