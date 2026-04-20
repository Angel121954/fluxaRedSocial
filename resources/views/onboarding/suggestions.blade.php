@extends('layouts.app')

@section('content')
<div class="page">

    <!-- LEFT -->
    <div class="left">

        <a href="#" class="logo">
            <img src="{{ asset('img/logoFluxa.png') }}" alt="Fluxa">
        </a>

        <div class="progress-bar">
            <span class="done"></span>
            <span class="done"></span>
            <span class="active"></span>
        </div>

        <h1>Desarrolladores que quizás conozcas</h1>
        <p class="subtitle">Paso 3 de 3 · Sigue desarrolladores para construir tu feed</p>

        <form action="{{ route('onboarding.saveSuggestions') }}" method="POST" id="suggestionsForm">
            @csrf
            <div class="users-grid" id="usersGrid">
                @forelse($suggested as $dev)
                {{-- @var \App\Models\User $dev --}}
                <div class="user-card">
                    <div class="avatar">
                        @if(isset($dev->avatar) && $dev->avatar)
                        <img src="{{ $dev->avatar }}" alt="{{ $dev->username }}">
                        @else
                        {{ strtoupper(substr($dev->name, 0, 1)) }}
                        @endif
                    </div>
                    <div class="user-info">
                        <span class="user-name">{{ $dev->name }}</span>
                        <div class="user-meta">
                            <span>&#64;{{ $dev->username }}</span>
                            @if($dev->role)
                            <span class="tag">{{ ucfirst($dev->role) }}</span>
                            @endif
                        </div>
                    </div>
                    <button type="button" class="follow-btn" onclick="toggleFollow(this, {{ $dev->id }})">
                        Seguir
                    </button>
                    <input type="hidden" name="follow[]" value="" id="follow_{{ $dev->id }}" disabled>
                </div>
                @empty
                <p style="color: var(--muted); font-size: 13px; text-align: center;">
                    No hay desarrolladores sugeridos por ahora.
                </p>
                @endforelse
            </div>

            <button type="submit" class="btn">Ir a Explorar →</button>
        </form>

        <p class="skip">
            <a href="{{ route('explore.index') }}" onclick="skipOnboarding(event)">Omitir e ir a Explorar</a>
        </p>
    </div>

    <!-- RIGHT -->
    <div class="right">
        <img
            class="illustration"
            src="{{ asset('img/desarrolladorRegistro.png') }}"
            alt="Desarrolladores sugeridos">

        <p class="tagline"><span>¡Ya casi terminas!</span><br>Empieza a construir tu red</p>

        <div class="welcome-badge">
            <strong>Bienvenido a Fluxa</strong>
            <p>Sigue desarrolladores para obtener un feed<br>personalizado lleno de proyectos e ideas.</p>
        </div>
    </div>

</div>
@endsection

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;500;600;700&family=DM+Sans:wght@400;500&display=swap" rel="stylesheet">
@vite('resources/css/onboarding/suggestions.css')
@endpush

@push('scripts')
@vite('resources/js/onboarding/index.js')
@endpush