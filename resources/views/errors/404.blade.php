<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">
    <title>404 — Fluxa</title>
    <link rel="icon" type="image/png" href="{{ asset('img/favicon.png') }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    @vite([
        'resources/css/variables.css',
        'resources/css/app.css',
        'resources/css/errors/404.css',
        'resources/js/errors/theme.js',
    ])
</head>
<body class="nf-page">
    <main class="nf-shell">
        <a href="{{ url('/') }}" class="nf-brand" aria-label="Fluxa — volver al inicio">
            <img src="{{ asset('img/logo-fluxa.png') }}" alt="Fluxa" height="30">
        </a>

        <p class="nf-code">404</p>

        <h1 class="nf-title">Esta ruta no existe</h1>
        <p class="nf-desc">Puede que el enlace esté roto o que la página haya cambiado de nombre.</p>

        <div class="nf-actions">
            <a href="{{ url('/') }}" class="nf-btn nf-btn--primary">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true" focusable="false">
                    <path d="M19 12H5M12 19l-7-7 7-7"/>
                </svg>
                Volver al inicio
            </a>
            @auth
                <a href="{{ route('explore.index') }}" class="nf-btn nf-btn--ghost">
                    Explorar proyectos
                </a>
            @endauth
        </div>
    </main>
</body>
</html>
