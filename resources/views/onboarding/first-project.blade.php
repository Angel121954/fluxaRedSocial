@extends('layouts.app')

@section('content')
<div class="page">

    <!-- LEFT -->
    <div class="left">

        <a href="#" class="logo">
            <img src="{{ asset('img/logo-fluxa.png') }}" alt="Fluxa">
        </a>

        <div class="progress-bar">
            <span class="done"></span>
            <span class="done"></span>
            <span class="done"></span>
            <span class="done"></span>
            <span class="done"></span>
            <span class="active"></span>
        </div>

        <h1>Publica tu primer proyecto</h1>
        <p class="subtitle">Paso 6 de 6 · Es la forma más rápida de que la comunidad te conozca</p>

        @if($canPublish)
        <div class="first-project-options">
            <button type="button" class="fp-option fp-option--primary" onclick="abrirModal()">
                <span class="fp-option__icon" aria-hidden="true">
                    <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                </span>
                <span class="fp-option__body">
                    <strong>Crear proyecto manualmente</strong>
                    <span>Título, descripción, tecnologías e imágenes. Toma menos de 2 minutos.</span>
                </span>
            </button>

            <a href="{{ route('github.connect') }}" class="fp-option">
                <span class="fp-option__icon" aria-hidden="true">
                    <svg width="20" height="20" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 0C5.37 0 0 5.37 0 12c0 5.31 3.435 9.795 8.205 11.385.6.105.825-.255.825-.57 0-.285-.015-1.23-.015-2.235-3.015.555-3.795-.735-4.035-1.41-.135-.345-.72-1.41-1.23-1.695-.42-.225-1.02-.78-.015-.795.945-.015 1.62.87 1.845 1.23 1.08 1.815 2.805 1.305 3.495.99.105-.78.42-1.305.765-1.605-2.67-.3-5.46-1.335-5.46-5.925 0-1.305.465-2.385 1.23-3.225-.12-.3-.54-1.53.12-3.18 0 0 1.005-.315 3.3 1.23.96-.27 1.98-.405 3-.405s2.04.135 3 .405c2.295-1.56 3.3-1.23 3.3-1.23.66 1.65.24 2.88.12 3.18.765.84 1.23 1.905 1.23 3.225 0 4.605-2.805 5.625-5.475 5.925.435.375.81 1.095.81 2.22 0 1.605-.015 2.895-.015 3.3 0 .315.225.69.825.57A12.02 12.02 0 0024 12c0-6.63-5.37-12-12-12z" />
                    </svg>
                </span>
                <span class="fp-option__body">
                    <strong>Importar desde GitHub</strong>
                    <span>Conecta tu cuenta y convierte un repositorio en proyecto automáticamente.</span>
                </span>
            </a>
        </div>
        @else
        <div class="fp-verify">
            <strong>Verifica tu correo para publicar</strong>
            <p>Enviamos un enlace de verificación a <strong>{{ Auth::user()->email }}</strong>. Una vez verificado, podrás publicar tu primer proyecto.</p>
            <a href="{{ route('verification.notice') }}" class="fp-option fp-option--primary">
                <span class="fp-option__icon" aria-hidden="true">
                    <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                </span>
                <span class="fp-option__body">
                    <strong>Verificar mi correo</strong>
                    <span>Revisa tu bandeja de entrada o reenvía el enlace.</span>
                </span>
            </a>
        </div>
        @endif

        <p class="skip">
            <a href="{{ route('feed.index') }}">Ahora no, ir a mi feed</a>
        </p>
    </div>

    <!-- RIGHT -->
    <div class="right">
        <img
            class="illustration"
            src="{{ asset('img/desarrolladorRegistro.png') }}"
            alt="Publica tu primer proyecto">

        <p class="tagline"><span>Tu trabajo merece ser visto</span><br>Empieza construyendo en público</p>

        <div class="welcome-badge">
            <strong>¿Por qué publicar ahora?</strong>
            <p>Un proyecto publicado te da un enlace compartible, feedback real y presencia en tu perfil. Es tu portafolio vivo dentro de la comunidad.</p>
        </div>
    </div>

</div>

@if($canPublish)
<x-new-project />
@endif
@endsection

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;500;600;700&family=DM+Sans:wght@400;500&display=swap" rel="stylesheet">
@vite('resources/css/onboarding/first-project.css')
@endpush
