@props(['services' => []])
<section class="section-padding bg-gray-50 dark:bg-gray-900">
    <div class="container-custom">
        {{-- Section Header --}}
        <div class="text-center max-w-3xl mx-auto mb-16">
            <span class="inline-block text-medical-blue dark:text-light-gold font-semibold mb-4">{{ __('servicesEyebrow') }}</span>
            <h2 class="heading-primary mb-6">{{ __('servicesHeading') }}</h2>
            <p class="text-gray-600 dark:text-gray-400 text-lg">
                {{ __('servicesHomeSubtitle') }}
            </p>
        </div>

        {{-- Services Grid --}}
        <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
            @foreach ($services as $service)
                <x-ui.card hover class="group overflow-hidden">
                    <x-ui.card-content class="p-0">
                        {{-- Icon Header --}}
                        <div class="bg-gradient-to-r {{ $service['color'] }} p-6">
                            <div class="w-16 h-16 bg-white/20 rounded-2xl flex items-center justify-center mb-4">
                                @svg('lucide-' . $service['icon'], 'w-8 h-8 text-white')
                            </div>
                            <h3 class="text-xl font-bold text-white mb-2">{{ $service['title'] }}</h3>
                        </div>

                        {{-- Content --}}
                        <div class="p-6">
                            <p class="text-gray-600 dark:text-gray-400 mb-4 line-clamp-3">{{ $service['description'] }}</p>

                            <ul class="space-y-2 mb-6">
                                @foreach ($service['features'] as $feature)
                                    <li class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400">
                                        <span class="w-1.5 h-1.5 rounded-full bg-medical-blue"></span>
                                        {{ $feature }}
                                    </li>
                                @endforeach
                            </ul>

                            <x-ui.button href="{{ $service['href'] }}" variant="ghost"
                                         class="w-full group-hover:bg-medical-blue group-hover:text-white transition-all">
                                {{ __('learnMore') }}
                                <x-slot:rightIcon>@svg('lucide-arrow-left', 'w-[18px] h-[18px]')</x-slot:rightIcon>
                            </x-ui.button>
                        </div>
                    </x-ui.card-content>
                </x-ui.card>
            @endforeach
        </div>

        {{-- CTA --}}
        <div class="text-center mt-12">
            <x-ui.button href="/services" variant="primary" size="lg">
                {{ __('viewAllServices') }}
                <x-slot:rightIcon>@svg('lucide-arrow-left', 'w-5 h-5')</x-slot:rightIcon>
            </x-ui.button>
        </div>
    </div>
</section>
