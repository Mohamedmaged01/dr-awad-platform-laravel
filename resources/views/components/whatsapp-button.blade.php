@props([
    'phoneNumber' => '201234567890',
    'message' => 'مرحباً، أريد الاستفسار عن خدماتكم',
])
@php $url = 'https://wa.me/' . $phoneNumber . '?text=' . rawurlencode($message); @endphp
<a href="{{ $url }}" target="_blank" rel="noopener noreferrer"
   class="fixed bottom-6 start-6 z-50 flex items-center gap-3 bg-green-500 text-white px-4 py-3 rounded-full shadow-lg hover:bg-green-600 transition-all duration-300 hover:scale-105 group"
   aria-label="Chat on WhatsApp">
    @svg('lucide-message-circle', 'w-6 h-6 animate-pulse')
    <span class="hidden md:inline font-medium">واتساب</span>
</a>
