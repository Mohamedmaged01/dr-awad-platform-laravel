<!DOCTYPE html>
<html lang="ar" dir="rtl" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'د. محمد عوض | استشاري النساء والتوليد والحقن المجهري')</title>
    <meta name="description" content="@yield('description', 'منصة متكاملة لعيادة الدكتور محمد عوض المتخصصة في النساء والتوليد، الحقن المجهري وأطفال الأنابيب، وجراحات المناظير. احجزي موعدك الآن.')">
    <meta name="keywords" content="دكتور نساء وتوليد، حقن مجهري، أطفال أنابيب، جراحات مناظير، علاج العقم، متابعة الحمل، القاهرة">

    {{-- Set the theme class before paint to avoid a flash (mirrors Header.tsx useEffect). --}}
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
<body class="font-cairo antialiased">
    <main class="min-h-screen @yield('main-class')" dir="rtl">
        <x-layout.header />

        @yield('content')

        <x-layout.footer />
        <x-whatsapp-button />
    </main>
</body>
</html>
