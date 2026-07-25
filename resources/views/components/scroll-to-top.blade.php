{{-- Mirrors ScrollToTop.tsx: appears past 300px, opposite corner from the WhatsApp FAB. --}}
<button x-data="{ show: false }"
        @scroll.window="show = window.scrollY > 300"
        @click="window.scrollTo({ top: 0, behavior: 'smooth' })"
        x-cloak
        :class="show ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-10 pointer-events-none'"
        class="fixed bottom-6 right-6 z-50 p-3 rounded-full bg-medical-blue text-white shadow-lg hover:scale-110 transition-all duration-300"
        aria-label="العودة للأعلى">
    @svg('lucide-chevron-up', 'w-6 h-6', ['stroke-width' => '3'])
</button>
