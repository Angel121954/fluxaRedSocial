@extends('layouts.app')
@section('title', 'Acerca de Fluxa')
@section('content')
<x-topbar :profile="$profile" />

<!-- ══════════════════════════════════════════
     HERO SECTION
═════════════════════════════════════════ -->
<section class="hero">
    <div class="hero-container">
        <div class="hero-content">
            <span class="hero-badge">
                <img src="{{ asset('img/logoFluxa.png') }}" alt="Fluxa" />
                Sobre Fluxa
            </span>
            <h1 class="hero-title">Construyendo en <span class="hero-title-accent">comunidad</span></h1>
            <p class="hero-subtitle">
                Una plataforma para desarrolladores que quieren compartir su progreso,
                aprender juntos y crecer como comunidad.
            </p>
        </div>
        <div class="hero-visual">
            <div class="hero-logo-wrap">
                <img src="{{ asset('img/logotipoAzulTurquesa.png') }}" alt="Fluxa" class="hero-logo-img" />
            </div>
            <div class="hero-decoration"></div>
        </div>
    </div>
</section>

<!-- ══════════════════════════════════════════
     INTRO SECTION
═════════════════════════════════════════ -->
<section class="intro">
    <div class="intro-container">
        <div class="intro-card">
            <div class="intro-icon-wrap">
                <img src="{{ asset('img/logotipoAzulTurquesa.png') }}" alt="" />
            </div>
            <div class="intro-text">
                <h2>Nuestra historia</h2>
                <p class="intro-label">El origen de Fluxa</p>
                <p>
                    Fluxa fue creada por <strong>Ángel David Agudelo Cuartas</strong> como una red social
                    pensada para compartir ideas, proyectos y crecimiento en el mundo tecnológico.
                </p>
                <p>
                    Nuestro logo, representado por una hormiga, <strong>simboliza disciplina, constancia y construcción diaria</strong>. Así como una hormiga trabaja en equipo y construye paso a paso,
                    Fluxa nace con la visión de crecer de forma sólida, inteligente y perseverante.
                </p>
                <p>
                    Un espacio donde cada proyecto importa, cada línea de código cuenta,
                    y cada desarrollador tiene voz.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- ══════════════════════════════════════════
     VALUES SECTION
═════════════════════════════════════════ -->
<section class="values">
    <div class="values-container">
        <div class="values-header">
            <span class="values-label">Qué nos define</span>
            <h2>Nuestros valores</h2>
        </div>
        <div class="values-grid">
            <div class="value-card">
                <div class="value-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/>
                    </svg>
                </div>
                <h3>Construido desde cero</h3>
                <p>Cada línea de código escrita con propósito y dedicación</p>
            </div>
            <div class="value-card">
                <div class="value-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M9 19c-5 1.5-5-2.5-7-3m14 6v-3.87a3.37 3.37 0 0 0-.94-2.61c3.14-.35 6.44-1.54 6.44-7A5.44 5.44 0 0 0 20 4.77 5.07 5.07 0 0 0 19.91 1S18.73.65 16 2.48a13.38 13.38 0 0 0-7 0C6.27.65 5.09 1 5.09 1A5.07 5.07 0 0 0 5 4.77a5.44 5.44 0 0 0-1.5 3.78c0 5.42 3.3 6.61 6.44 7A3.37 3.37 0 0 0 9 18.13V22"/>
                    </svg>
                </div>
                <h3>Open Source</h3>
                <p>Transparencia y código abierto próximamente</p>
            </div>
            <div class="value-card">
                <div class="value-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                        <circle cx="9" cy="7" r="4"/>
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/>
                    </svg>
                </div>
                <h3>Comunidad</h3>
                <p>Creemos juntos, aprendemos juntos</p>
            </div>
            <div class="value-card">
                <div class="value-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/>
                    </svg>
                </div>
                <h3>Innovación</h3>
                <p>Mejora continua y experimentación constante</p>
            </div>
        </div>
    </div>
</section>

<!-- ══════════════════════════════════════════
     STATS SECTION
═════════════════════════════════════════ -->
<section class="stats">
    <div class="stats-container">
        <div class="stats-card">
            <div class="stats-header">
                <h2>Fluxa en números</h2>
                <p>Una comunidad en crecimiento comprometida con compartir conocimiento</p>
            </div>
            <div class="stats-grid">
                <div class="stat-item">
                    <span class="stat-number">100<small>%</small></span>
                    <span class="stat-label">Desde cero</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number">24<small>/7</small></span>
                    <span class="stat-label">Disponible</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number">∞</span>
                    <span class="stat-label">Proyectos</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number">1</span>
                    <span class="stat-label">Comunidad</span>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ══════════════════════════════════════════
     MISSION SECTION
═════════════════════════════════════════ -->
<section class="mission">
    <div class="mission-container">
        <div class="mission-card">
            <div class="mission-visual">
                <img src="{{ asset('img/logotipoAzulTurquesa.png') }}" alt="Fluxa" />
            </div>
            <div class="mission-content">
                <span class="mission-label">Nuestra misión</span>
                <h3>Construir en público</h3>
                <p>
                    Crear un espacio donde desarrolladores y creadores puedan
                    <strong>construir en público</strong>, compartir su progreso,
                    recibir feedback genuino y crecer como comunidad. No se trata solo
                    de publicar proyectos terminados, sino de
                    <strong>documentar el proceso</strong>, celebrar los pequeños logros
                    y aprender de los desafíos.
                </p>
            </div>
        </div>
    </div>
</section>

<x-footer />
@endsection

@push('styles')
@vite('resources/css/pages/aboutFluxa.css')
@endpush
