<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="ltr">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Fluxa - Plataforma para desarrolladores que quieren compartir su progreso, aprender juntos y crecer como comunidad.">
    <meta name="keywords" content="Fluxa, desarrollo, programación, comunidad, proyectos, open source">

    <!-- Open Graph -->
    <meta property="og:title" content="@yield('title', 'Fluxa')">
    <meta property="og:description" content="Plataforma para desarrolladores que quieren compartir su progreso, aprender juntos y crecer como comunidad.">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ request()->url() }}">
    <meta property="og:image" content="{{ asset('img/logoFluxa.png') }}">

    <title>@yield('title', 'Fluxa')</title>
    <link rel="icon" type="image/png" href="{{ asset('img/logoFluxa.png') }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="{{ asset('css/variables.css') }}">
    <link rel="stylesheet" href="{{ asset('css/profile/shared.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/devicons/devicon@latest/devicon.min.css">
    @stack('styles')
</head>

<body class="font-sans antialiased">
    @yield('content')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <div class="toast" id="toast" style="display:none;">
        <span id="toastMessage"></span>
    </div>

    @if(session('success'))
    <script>
        (function() {
            const toast = document.getElementById('toast');
            const msg = document.getElementById('toastMessage');
            msg.textContent = '{{ session('success') }}';
            toast.style.cssText = 'position:fixed;bottom:1.5rem;right:1.5rem;background:#12b3b6;color:#fff;padding:0.75rem 1.25rem;border-radius:0.5rem;font-size:0.9rem;z-index:9999;';
            toast.style.display = 'block';
            setTimeout(() => toast.style.display = 'none', 3000);
        })();
    </script>
    @endif

    @stack('scripts')

    <script src="{{ asset('js/modalScrollFix.js') }}"></script>
</body>

</html>