@props(['faqs' => []])
{{-- Mirrors FAQSection.tsx: first item open by default, click toggles. --}}
<section class="section-padding" x-data="{ open: 0 }">
    <div class="container-custom">
        {{-- Section Header --}}
        <div class="text-center max-w-3xl mx-auto mb-16">
            <span class="inline-block text-medical-blue dark:text-light-gold font-semibold mb-4">{{ __('faqEyebrow') }}</span>
            <h2 class="heading-primary mb-6">{{ __('faqTitle') }}</h2>
            <p class="text-gray-600 dark:text-gray-400 text-lg">{{ __('faqSubtitle') }}</p>
        </div>

        {{-- FAQ Accordion --}}
        <div class="max-w-3xl mx-auto space-y-4">
            @foreach ($faqs as $index => $faq)
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md overflow-hidden transition-all duration-300"
                     :class="open === {{ $index }} && 'ring-2 ring-medical-blue'">
                    <button @click="open = (open === {{ $index }} ? null : {{ $index }})"
                            class="w-full flex items-center justify-between p-6 text-start">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center transition-colors flex-shrink-0"
                                 :class="open === {{ $index }} ? 'bg-medical-blue text-white' : 'bg-gray-100 dark:bg-gray-700 text-medical-blue'">
                                @svg('lucide-help-circle', 'w-5 h-5')
                            </div>
                            <h3 class="font-semibold text-gray-800 dark:text-white">{{ $faq['question'] }}</h3>
                        </div>
                        @svg('lucide-chevron-down', [
                            'class' => 'w-6 h-6 text-gray-400 transition-transform duration-300 flex-shrink-0',
                            ':class' => "open === {$index} && 'rotate-180 text-medical-blue'",
                        ])
                    </button>
                    <div class="overflow-hidden transition-all duration-300"
                         :class="open === {{ $index }} ? 'max-h-96' : 'max-h-0'">
                        <div class="px-6 pb-6 pt-0">
                            <div class="ps-14">
                                <p class="text-gray-600 dark:text-gray-400 leading-relaxed">{{ $faq['answer'] }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- CTA --}}
        <div class="text-center mt-12">
            <p class="text-gray-600 dark:text-gray-400 mb-4">{{ __('faqCtaText') }}</p>
            <a href="https://wa.me/{{ config('clinic.contact.whatsapp') }}"
               class="inline-flex items-center gap-2 text-medical-blue dark:text-light-gold font-semibold hover:underline">
                {{ __('contactViaWhatsApp') }}
            </a>
        </div>
    </div>
</section>
