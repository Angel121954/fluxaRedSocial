<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>404 — Fluxa</title>
    <link rel="icon" type="image/png" href="{{ asset('img/favicon.png') }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/css/variables.css'])
    <style>
        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--bg);
            font-family: 'Figtree', sans-serif;
            color: var(--ink-700);
        }
        .error-page {
            text-align: center;
            padding: 2rem;
        }
        .error-code {
            font-size: 6rem;
            font-weight: 700;
            color: var(--accent);
            line-height: 1;
            margin-bottom: 0.5rem;
            letter-spacing: -0.04em;
        }
        .error-title {
            font-size: var(--text-xl);
            font-weight: 600;
            color: var(--ink-900);
            margin-bottom: 0.5rem;
        }
        .error-desc {
            font-size: var(--text-base);
            color: var(--ink-500);
            margin-bottom: 2rem;
            max-width: 360px;
        }
        .error-link {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.625rem 1.25rem;
            border-radius: var(--r-md);
            background: var(--accent);
            color: #fff;
            font-size: var(--text-base);
            font-weight: 600;
            text-decoration: none;
            transition: background 0.15s;
        }
        .error-link:hover {
            background: var(--accent-dark);
        }
    </style>
</head>
<body>
    <div class="error-page">
        <div class="error-code">404</div>
        <h1 class="error-title">Página no encontrada</h1>
        <p class="error-desc">Esto ya no existe o nunca existió.</p>
        <a href="{{ url('/') }}" class="error-link">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <path d="M19 12H5M12 19l-7-7 7-7"/>
            </svg>
            Volver al inicio
        </a>
    </div>
</body>
</html>
