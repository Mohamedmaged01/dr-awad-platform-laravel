@extends('layouts.public')

@section('title', $pageTitle . ' | د. محمد عوض')

@section('content')
    <section class="pt-32 pb-16 gradient-medical">
        <div class="container-custom text-center text-white">
            <h1 class="text-4xl md:text-5xl font-bold mb-4">{{ $pageTitle }}</h1>
        </div>
    </section>

    <section class="py-16">
        <div class="container-custom">
            <x-ui.card class="max-w-3xl mx-auto">
                <x-ui.card-content class="py-16 text-center">
                    <div class="w-20 h-20 mx-auto mb-6 rounded-full bg-medical-blue/10 flex items-center justify-center">
                        @svg('lucide-file-text', 'w-10 h-10 text-medical-blue')
                    </div>
                    <h2 class="text-2xl font-bold text-gray-800 dark:text-white mb-4">هذه الصفحة قيد الإعداد</h2>
                    <p class="text-gray-600 dark:text-gray-400 mb-8">سيتم إضافة المحتوى قريباً.</p>
                    <x-ui.button href="/" variant="primary">العودة للرئيسية</x-ui.button>
                </x-ui.card-content>
            </x-ui.card>
        </div>
    </section>
@endsection
