@extends('layouts.app')

@section('content')
<div class="page">

    <!-- LEFT -->
    <div class="left">

        <a href="#" class="logo">
            <img src="{{ asset('img/logoFluxa.png') }}" alt="Fluxa">
        </a>

        <div class="progress-bar">
            <span class="active"></span>
            <span></span>
            <span></span>
        </div>

        <h1>¿Cuál es tu stack tecnológico?</h1>
        <p class="subtitle">Paso 1 de 3 · Selecciona las tecnologías con las que trabajas</p>

        <div class="search-wrap">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="11" cy="11" r="8" />
                <path d="m21 21-4.35-4.35" />
            </svg>
            <input type="text" placeholder="Buscar tecnología..." id="searchInput" oninput="filterTech(this.value)">
        </div>

        <form action="{{ route('onboarding.saveTechnologies') }}" method="POST">
            @csrf
            <div class="tech-grid" id="techGrid">
                @forelse($technologies as $tech)
                <div class="tech-item" data-name="{{ strtolower($tech->name) }}">
                    <input type="checkbox" name="technologies[]" value="{{ $tech->id }}" id="tech_{{ $tech->id }}">
                    <label for="tech_{{ $tech->id }}">
                        <div class="check">
                            <svg viewBox="0 0 10 10" fill="none">
                                <path d="M2 5l2.5 2.5L8 3" stroke="#fff" stroke-width="1.5" stroke-linecap="round" />
                            </svg>
                        </div>
                        <i class="devicon-{{ $tech->slug }}-plain colored"></i>
                        {{ $tech->name }}
                    </label>
                </div>
                @empty
                <p style="color: var(--muted); font-size: 13px; grid-column: 1/-1; text-align: center;">
                    No se encontraron tecnologías.
                </p>
                @endforelse
            </div>

            <button type="submit" class="btn">Continuar →</button>
        </form>

        <p class="skip"><a href="{{ route('onboarding.role') }}">Omitir por ahora</a></p>
    </div>

    <!-- RIGHT -->
    <div class="right">
        <img
            class="illustration"
            src="{{ asset('img/desarrolladorRegistro.png') }}"
            alt="Elige tus tecnologías">

        <p class="tagline"><span>Construye tu perfil</span> con<br>las herramientas que usas</p>

        <div class="badges">
            <span class="badge">Feed personalizado</span>
            <span class="badge">Encuentra colaboradores</span>
            <span class="badge">Muestra tus proyectos</span>
        </div>
    </div>

</div>
@endsection

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;500;600;700&family=DM+Sans:wght@400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/technologies.css') }}">
@endpush

@push('scripts')
<script src="{{ asset('js/onboarding/technologies.js') }}"></script>
@endpush