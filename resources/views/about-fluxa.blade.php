@extends('layouts.app')
@section('title', 'Acerca de Fluxa')
@section('content')
@include('components.topbar')
<!-- ══════════════════════════════════════════
     HERO SECTION
══════════════════════════════════════════ -->
<section class="hero">
    <div class="hero-badge">
        <img src="{{ asset('img/logoFluxa.png') }}" alt="Fluxa icon" />
        Sobre Fluxa
    </div>

    <h1 class="hero-title">Construyendo en comunidad</h1>
    <p class="hero-subtitle">
        Una plataforma para desarrolladores que quieren compartir su progreso,
        aprender juntos y crecer como comunidad.
    </p>

    <div class="hero-logo">
        <img
            src="{{ asset('img/logotipoAzulTurquesa.png') }}"
            alt="Fluxa - Hormiga simbolizando construcción y comunidad" />
    </div>
</section>

<!-- ══════════════════════════════════════════
     STORY SECTION
══════════════════════════════════════════ -->
<section class="story">
    <div class="story-card">
        <div class="story-content">
            <h2>Nuestra historia</h2>
            <p class="subtitle">El origen de Fluxa</p>

            <p>
                Fluxa fue creada por
                <strong>Ángel David Agudelo Cuartas</strong> como una red social
                pensada para compartir ideas, proyectos y crecimiento en el mundo
                tecnológico.
            </p>

            <p>
                Nuestro logo, representado por una hormiga,
                <strong>simboliza disciplina, constancia y construcción diaria</strong>. Así como una hormiga trabaja en equipo y construye paso a paso,
                Fluxa nace con la visión de crecer de forma sólida, inteligente y
                perseverante.
            </p>

            <p>
                Un espacio donde cada proyecto importa, cada línea de código cuenta,
                y cada desarrollador tiene voz.
            </p>
        </div>

        <div class="story-image">
            <img src="{{ asset('img/logotipoAzulTurquesa.png') }}" alt="Fluxa ant logo" />
        </div>
    </div>
</section>

<!-- ══════════════════════════════════════════
     VALUES SECTION
══════════════════════════════════════════ -->
<section class="values">
    <div class="section-header">
        <h2>Nuestros valores</h2>
        <p>Los principios que guían cada decisión en Fluxa</p>
    </div>

    <div class="values-grid">
        <div class="value-card">
            <div class="value-icon">🔨</div>
            <h3 class="value-title">Construido desde cero</h3>
            <p class="value-desc">
                Cada línea de código escrita con propósito y dedicación
            </p>
        </div>

        <div class="value-card">
            <div class="value-icon">&lt;/&gt;</div>
            <h3 class="value-title">Open Source</h3>
            <p class="value-desc">Transparencia y código abierto próximamente</p>
        </div>

        <div class="value-card">
            <div class="value-icon">👥</div>
            <h3 class="value-title">Comunidad</h3>
            <p class="value-desc">Crecemos juntos, aprendemos juntos</p>
        </div>

        <div class="value-card">
            <div class="value-icon">⚡</div>
            <h3 class="value-title">Innovación</h3>
            <p class="value-desc">Mejora continua y experimentación constante</p>
        </div>
    </div>
</section>

<!-- ══════════════════════════════════════════
     STATS SECTION
══════════════════════════════════════════ -->
<section class="stats">
    <div class="stats-card">
        <h2 class="stats-title">Fluxa en números</h2>
        <p class="stats-subtitle">
            Una comunidad en crecimiento comprometida con compartir conocimiento y
            construir en público
        </p>

        <div class="stats-grid">
            <div class="stat-item">
                <div class="stat-number">100%</div>
                <div class="stat-label">Desde cero</div>
            </div>

            <div class="stat-item">
                <div class="stat-number">24/7</div>
                <div class="stat-label">Disponible</div>
            </div>

            <div class="stat-item">
                <div class="stat-number">∞</div>
                <div class="stat-label">Proyectos</div>
            </div>

            <div class="stat-item">
                <div class="stat-number">1</div>
                <div class="stat-label">Comunidad</div>
            </div>
        </div>
    </div>
</section>

@include('components.mission')
@include('components.footer')
@endsection

@push('styles')
<!--Estilo personalizado de sobre fluxa-->
<link rel="stylesheet" href="{{ asset('css/aboutFluxa.css') }}" />
@endpush