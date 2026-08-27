@extends('layouts.app')
@section('title', 'Fluxa — Comparte tu progreso, crece en comunidad')
@section('content')

{{-- Saltar al contenido (a11y) --}}
<a class="landing-skip" href="#landing-main">Saltar al contenido</a>

{{-- ═══════════════ NAVBAR PÚBLICA ═══════════════ --}}
<header class="landing-nav">
    <div class="landing-container landing-nav__inner">
        <a href="#landing-top" class="landing-nav__brand" aria-label="Fluxa — inicio">
            <img src="{{ asset('img/logo.png') }}" alt="Fluxa" class="landing-nav__logo" height="32" />
        </a>

        <nav class="landing-nav__links" aria-label="Navegación principal">
            <a href="#caracteristicas">Características</a>
            <a href="#como-funciona">Guía</a>
            <a href="#comunidad">Comunidad</a>
        </nav>

        <div class="landing-nav__cta">
            <a href="{{ route('login') }}" class="landing-btn landing-btn--ghost">Iniciar sesión</a>
            <a href="{{ route('register') }}" class="landing-btn landing-btn--primary">Registrarse</a>
        </div>

        <button class="landing-nav__burger" id="landingMenuBtn" aria-expanded="false" aria-label="Menú">
            <span></span><span></span><span></span>
        </button>
    </div>

    <!-- Menú móvil -->
    <div class="landing-menu" id="landingMenu" role="dialog" aria-hidden="true">
        <div class="landing-menu__inner">
            <a href="#caracteristicas">Características</a>
            <a href="#como-funciona">Guía</a>
            <a href="#comunidad">Comunidad</a>
            <hr />
            <a href="{{ route('login') }}">Iniciar sesión</a>
            <a href="{{ route('register') }}" class="landing-btn landing-btn--primary" style="display:inline-flex;">Registrarse</a>
        </div>
    </div>
</header>

<main id="landing-main">

    {{-- ═══════════════ HERO ═══════════════ --}}
    <section class="landing-hero" id="landing-top">
        <div class="landing-container landing-hero__grid">
            <div class="landing-hero__content">
                <span class="landing-tag">Red social para desarrolladores</span>

                <h1 class="landing-hero__title">
                    Comparte tu progreso.<br />
                    <span class="landing-hero__highlight">Crece en comunidad.</span>
                </h1>

                <p class="landing-hero__description">
                    Publica tus proyectos, recibe feedback y conéctate con otros desarrolladores latinoamericanos.
                </p>

                <div class="landing-hero__actions">
                    <a href="{{ route('register') }}" class="landing-btn landing-btn--primary">
                        Comenzar gratis
                    </a>
                    <a href="{{ route('auth.guest') }}" class="landing-btn landing-btn--outline">
                        Explorar
                    </a>
                </div>

                <div class="landing-hero__stats">
                    <div>
                        <span class="landing-hero__stat-number">{{ number_format($stats['developers']) }}</span>
                        <span class="landing-hero__stat-label">Desarrolladores</span>
                    </div>
                    <div>
                        <span class="landing-hero__stat-number">{{ number_format($stats['projects']) }}</span>
                        <span class="landing-hero__stat-label">Proyectos</span>
                    </div>
                    <div>
                        <span class="landing-hero__stat-number">{{ number_format($stats['endorsements']) }}</span>
                        <span class="landing-hero__stat-label">Reconocimientos</span>
                    </div>
                </div>
            </div>

            <!-- Mockup más sutil -->
            <div class="landing-hero__visual" aria-hidden="true">
                @php
                $featured = $projects->first();
                $cover = null;
                if ($featured && $featured->media?->firstWhere('type', 'image')) {
                $cloud = config('cloudinary.cloud_name');
                $img = $featured->media->firstWhere('type', 'image');
                $cover = $img->public_id
                ? "https://res.cloudinary.com/{$cloud}/image/upload/c_fill,g_auto,q_auto:good,f_auto,w_600,h_500/{$img->public_id}"
                : $img->media_url;
                }
                @endphp

                <div class="landing-card">
                    <div class="landing-card__header">
                        <div class="landing-card__avatar" style="background-image: url('{{ $featured->user->avatar_url ?? '' }}')"></div>
                        <div class="landing-card__user">
                            <strong>{{ $featured->user->name ?? 'Usuario' }}</strong>
                            <span>{{ $featured->user->username ?? '@usuario' }}</span>
                        </div>
                    </div>

                    <h3 class="landing-card__title">{{ $featured->title ?? 'Proyecto destacado' }}</h3>

                    @if ($cover)
                    <div class="landing-card__image" style="background-image: url('{{ $cover }}')"></div>
                    @endif

                    <div class="landing-card__footer">
                        <span class="landing-card__likes">
                            <svg width="16" height="16" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                            </svg>
                            {{ $featured ? $featured->likes_count : 128 }}
                        </span>
                        <span class="landing-card__comments">{{ $featured ? $featured->comments_count : 12 }} comentarios</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ═══════════════ MARQUEE DE TECNOLOGÍAS ═══════════════ --}}
    <div class="landing-marquee" aria-hidden="true">
        <div class="landing-marquee__track">
            @php
            $stack = [
            'php' => 'PHP',
            'laravel' => 'Laravel',
            'javascript' => 'JavaScript',
            'react' => 'React',
            'vuejs' => 'Vue',
            'python' => 'Python',
            'nodejs' => 'Node.js',
            'mysql' => 'MySQL',
            'docker' => 'Docker',
            'tailwindcss' => 'Tailwind',
            'git' => 'Git',
            'figma' => 'Figma',
            'typescript' => 'TypeScript',
            'html5' => 'HTML',
            'css3' => 'CSS',
            ];
            $items = array_merge(array_keys($stack), array_keys($stack));
            @endphp
            @foreach ($items as $tech)
            <span class="landing-marquee__item">
                <i class="devicon-{{ $tech }}-plain colored"></i>
                {{ $stack[$tech] }}
            </span>
            @endforeach
        </div>
    </div>

    {{-- ═══════════════ CARACTERÍSTICAS ═══════════════ --}}
    <section class="landing-section" id="caracteristicas">
        <div class="landing-container">
            <div class="landing-section__head landing-reveal">
                <span class="landing-section__label">Por qué Fluxa</span>
                <h2 class="landing-section__title">Todo lo que un dev necesita para crecer</h2>
                <p class="landing-section__sub">Una sola plataforma para mostrar tu trabajo, aprender de otros y construir reputación real.</p>
            </div>

            <div class="landing-features">
                <article class="landing-feature landing-reveal">
                    <span class="landing-feature__icon">
                        <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 4h2a2 2 0 012 2v14a2 2 0 01-2 2H6a2 2 0 01-2-2V6a2 2 0 012-2h2m3-1h4a1 1 0 011 1v2a1 1 0 01-1 1H9a1 1 0 01-1-1V4a1 1 0 011-1zm-4 10h8m-8 4h8m-8-8h5" />
                        </svg>
                    </span>
                    <h3>Proyectos en público</h3>
                    <p>Publica tus proyectos con imágenes, video y descripciones. Documenta el proceso, no solo el resultado.</p>
                </article>

                <article class="landing-feature landing-reveal">
                    <span class="landing-feature__icon">
                        <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </span>
                    <h3>Feedback que impulsa</h3>
                    <p>Likes, comentarios y endorsements de habilidades. Sabes de verdad qué impacto generas.</p>
                </article>

                <article class="landing-feature landing-reveal">
                    <span class="landing-feature__icon">
                        <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 21s-7-4.9-7-11a5 5 0 0110-3.3A5 5 0 0119 10c0 6.1-7 11-7 11z" />
                            <circle cx="12" cy="10" r="2.5" />
                        </svg>
                    </span>
                    <h3>Comunidad LatAm</h3>
                    <p>Encuentra desarrolladores cercanos con el mapa y sigue a quienes te inspiran, en tu idioma y tu región.</p>
                </article>

                <article class="landing-feature landing-reveal">
                    <span class="landing-feature__icon">
                        <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 4v-4z" />
                        </svg>
                    </span>
                    <h3>Mensajería en tiempo real</h3>
                    <p>Chats instantáneos con indicador de escritura, GIFs y notificaciones al momento.</p>
                </article>

                <article class="landing-feature landing-reveal">
                    <span class="landing-feature__icon">
                        <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </span>
                    <h3>Perfil profesional</h3>
                    <p>Stack tecnológico, experiencia y educación, con un CV en PDF listo para descargar y compartir.</p>
                </article>

                <article class="landing-feature landing-reveal">
                    <span class="landing-feature__icon">
                        <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </span>
                    <h3>Construido por y para devs</h3>
                    <p>Nacido en el SENA y pensado para crecer con la comunidad. Cada sugerencia suma.</p>
                </article>
            </div>
        </div>
    </section>

    {{-- ═══════════════ CÓMO FUNCIONA ═══════════════ --}}
    <section class="landing-section landing-section--alt" id="como-funciona">
        <div class="landing-container">
            <div class="landing-section__head landing-reveal">
                <span class="landing-section__label">Empieza hoy</span>
                <h2 class="landing-section__title">Cómo funciona</h2>
            </div>

            <ol class="landing-steps">
                <li class="landing-step landing-reveal">
                    <span class="landing-step__num">1</span>
                    <h3>Crea tu perfil</h3>
                    <p>Regístrate en un minuto con tu correo o con GitHub y elige tus tecnologías favoritas.</p>
                </li>
                <li class="landing-step landing-reveal">
                    <span class="landing-step__num">2</span>
                    <h3>Comparte tu progreso</h3>
                    <p>Publica tus proyectos, documenta el proceso y recibe feedback honesto de la comunidad.</p>
                </li>
                <li class="landing-step landing-reveal">
                    <span class="landing-step__num">3</span>
                    <h3>Crece con la comunidad</h3>
                    <p>Sigue a otros devs, conversa, da reconocimientos y construye tu reputación profesional.</p>
                </li>
            </ol>
        </div>
    </section>

    {{-- ═══════════════ COMUNIDAD EN VIVO ═══════════════ --}}
    <section class="landing-section" id="comunidad">
        <div class="landing-container">
            <div class="landing-section__head landing-reveal">
                <span class="landing-section__label">Comunidad</span>
                <h2 class="landing-section__title">La comunidad ya está construyendo</h2>
                <p class="landing-section__sub">Un vistazo a los proyectos más recientes publicados por desarrolladores como tú.</p>
            </div>

            @if ($projects->isNotEmpty())
            <div class="landing-projects">
                @foreach ($projects as $project)
                <article class="landing-project landing-reveal">
                    @php
                    $cover = null;
                    $img = $project->media?->firstWhere('type', 'image');
                    if ($img) {
                    $cloud = config('cloudinary.cloud_name');
                    $cover = $img->public_id
                    ? "https://res.cloudinary.com/{$cloud}/image/upload/c_fill,g_auto,q_auto:good,f_auto,w_600,h_400/{$img->public_id}"
                    : $img->media_url;
                    }
                    @endphp

                    @if ($cover)
                    <img src="{{ $cover }}" alt="Imagen del proyecto {{ $project->title }}" class="landing-project__cover" loading="lazy" />
                    @else
                    <span class="landing-project__cover landing-project__cover--empty" aria-hidden="true">
                        <svg width="28" height="28" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                        </svg>
                    </span>
                    @endif

                    <div class="landing-project__body">
                        <div class="landing-project__author">
                            <img
                                src="{{ $project->user->avatar_url }}"
                                alt=""
                                class="landing-project__avatar"
                                loading="lazy" />
                            <span class="landing-project__meta">
                                <strong>{{ $project->user->name }}</strong>
                                <span>{{ $project->user->username ? '@' . $project->user->username : '' }}</span>
                            </span>
                        </div>

                        <h3 class="landing-project__title">{{ $project->title }}</h3>

                        @if ($project->technologies->isNotEmpty())
                        <div class="landing-project__tags">
                            @foreach ($project->technologies->take(3) as $tech)
                            <span class="landing-project__tag">{{ $tech->name }}</span>
                            @endforeach
                        </div>
                        @endif

                        <span class="landing-project__likes">
                            <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                            </svg>
                            {{ $project->likes_count }}
                        </span>
                    </div>
                </article>
                @endforeach
            </div>
            @else
            <p class="landing-empty">Aún no hay proyectos publicados. ¡Sé de los primeros en compartir el tuyo!</p>
            @endif
        </div>
    </section>

    {{-- ═══════════════ CTA FINAL ═══════════════ --}}
    <section class="landing-section">
        <div class="landing-container">
            <div class="landing-cta landing-reveal">
                <h2 class="landing-cta__title">Tu próximo gran proyecto empieza aquí</h2>
                <p class="landing-cta__sub">Únete a la red de desarrolladores que construyen en público todos los días.</p>
                <div class="landing-cta__actions">
                    <a href="{{ route('register') }}" class="landing-btn landing-btn--white landing-btn--lg">Crear cuenta gratis</a>
                    <a href="{{ route('auth.guest') }}" class="landing-btn landing-btn--ghost-light landing-btn--lg">Explorar como visitante</a>
                </div>
            </div>
        </div>
    </section>
</main>

{{-- ═══════════════ FOOTER ═══════════════ --}}
<footer class="landing-footer">
    <div class="landing-container landing-footer__inner">
        <div class="landing-footer__brand">
            <img src="{{ asset('img/logo.png') }}" alt="Fluxa" class="landing-footer__logo" />
            <p>Construido en público para la comunidad de desarrolladores latinoamericanos.</p>
        </div>

        <div class="landing-footer__links">
            <a href="{{ route('login') }}">Iniciar sesión</a>
            <a href="{{ route('register') }}">Crear cuenta</a>
            <a href="{{ route('auth.guest') }}">Explorar como visitante</a>
        </div>

        <div class="landing-footer__social">
            <a href="https://github.com/Angel121954" target="_blank" rel="noopener noreferrer" class="landing-footer__social-link" aria-label="GitHub">
                <svg width="18" height="18" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M12 0C5.37 0 0 5.37 0 12c0 5.31 3.435 9.795 8.205 11.385.6.105.825-.255.825-.57 0-.285-.015-1.23-.015-2.235-3.015.555-3.795-.735-4.035-1.41-.135-.345-.72-1.41-1.23-1.695-.42-.225-1.02-.78-.015-.795.945-.015 1.62.87 1.845 1.23 1.08 1.815 2.805 1.305 3.495.99.105-.78.42-1.305.765-1.605-2.67-.3-5.46-1.335-5.46-5.925 0-1.305.465-2.385 1.23-3.225-.12-.3-.54-1.53.12-3.18 0 0 1.005-.315 3.3 1.23.96-.27 1.98-.405 3-.405s2.04.135 3 .405c2.295-1.56 3.3-1.23 3.3-1.23.66 1.65.24 2.88.12 3.18.765.84 1.23 1.905 1.23 3.225 0 4.605-2.805 5.625-5.475 5.925.435.375.81 1.095.81 2.22 0 1.605-.015 2.895-.015 3.3 0 .315.225.69.825.57A12.02 12.02 0 0024 12c0-6.63-5.37-12-12-12z" />
                </svg>
            </a>
            <a href="https://www.linkedin.com/in/angel-david-agudelo-cuartas-547624345" target="_blank" rel="noopener noreferrer" class="landing-footer__social-link" aria-label="LinkedIn">
                <svg width="18" height="18" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z" />
                </svg>
            </a>
            <a href="https://www.instagram.com/cangeldavidagudelo" target="_blank" rel="noopener noreferrer" class="landing-footer__social-link" aria-label="Instagram">
                <svg width="18" height="18" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 8.333.072 9.147.2 4.407 1.622 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 8.333-.014 9.147-.072 4.407-.2 6.78-1.622 6.98-6.98.058-1.814.072-8.888.072-9.147 0-3.259-.014-8.333-.072-9.147-.2-4.407-1.622-6.78-6.98-6.98C20.667.014 14.667 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z" />
                </svg>
            </a>
        </div>
    </div>

    <div class="landing-container landing-footer__bottom">
        <p>© {{ date('Y') }} Fluxa · Hecho por y para la comunidad dev de Latinoamérica.</p>
    </div>
</footer>
@endsection

@push('styles')
<link rel="preconnect" href="https://fonts.bunny.net" />
<link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />
@vite('resources/css/public/landing.css')
@endpush

@push('scripts')
@vite('resources/js/public/landing.js')
@endpush