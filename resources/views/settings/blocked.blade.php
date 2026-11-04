@extends('layouts.app')
@section('title', 'Usuarios bloqueados')
@section('content')
<x-topbar :profile="$profile" />

<div class="edit-layout">
    <x-sidebar :profile="$profile" />

    <main class="main-content">
        <h1 class="page-title">Usuarios bloqueados</h1>
        <p class="page-subtitle">Las personas que bloqueaste no pueden interactuar contigo ni ver tu contenido.</p>

        @if($blockedUsers->isEmpty())
        <div class="blocked-empty">
            <div class="blocked-empty__icon" aria-hidden="true">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                </svg>
            </div>
            <h2 class="blocked-empty__title">No tienes usuarios bloqueados</h2>
            <p class="blocked-empty__text">
                Cuando bloquees a alguien, aparecerá aquí para que puedas gestionarlo o desbloquearlo.
            </p>
            <a href="{{ route('explore.trending') }}" class="btn btn-secondary blocked-empty__cta">
                Explorar comunidad
            </a>
        </div>
        @else
        <div class="toggle-group">
            @foreach($blockedUsers as $blockedUser)
            <div class="toggle-row">
                <div class="toggle-info blocked-user">
                    <img
                        src="{{ $blockedUser->avatar_url }}"
                        alt="{{ $blockedUser->name }}"
                        class="w-10 h-10 rounded-full object-cover" />
                    <div>
                        <a href="{{ route('profile.show', $blockedUser->username) }}" class="toggle-label">
                            {{ $blockedUser->name }}
                        </a>
                        <span class="toggle-desc">&#64;{{ $blockedUser->username }}</span>
                    </div>
                </div>

                <form method="POST" action="{{ route('users.unblock', $blockedUser) }}">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-secondary">Desbloquear</button>
                </form>
            </div>
            @endforeach
        </div>
        @endif
    </main>
</div>
@endsection

@push('styles')
@vite('resources/css/profile/shared.css')
@vite('resources/css/profile/sidebar.css')
@vite('resources/css/settings/privacy.css')
@vite('resources/css/settings/blocked.css')
@endpush
