@props(['profile'])

<!-- ══════════════════════════════════════════
     NAVBAR
═════════════════════════════════════════ -->
<nav class="navbar">
    <div class="navbar-inner">
        <div class="navbar-left">
            <a href="{{ route('explore.index') }}">
                <img src="{{ asset('img/logoFluxa.png') }}" alt="Logo de Fluxa" class="nav-logo" />
            </a>
            <nav class="nav-links">
                @if(Auth::user()->role !== 'guest')
                <a href="#"
                    class="nav-link {{ request()->routeIs('feed*') ? 'active' : '' }}">
                    Feed
                </a>
                @endif
                <a href="{{ route('explore.index') }}"
                    class="nav-link {{ request()->routeIs('explore*') ? 'active' : '' }}">
                    Explorar
                </a>
                @if(Auth::user()->role !== 'guest')
                <a href="{{ route('notifications.index') }}"
                    class="nav-link {{ request()->routeIs('notifications*') ? 'active' : '' }}">
                    Notificaciones
                </a>
                @endif
            </nav>
        </div>

        <div class="navbar-right">
            @if(Auth::user()->role === 'guest')
            <form action="{{ route('guest.destroy') }}" method="POST" style="display:inline">
                @csrf
                <button type="submit" class="btn-new danger">Salir</button>
            </form>
            @else
            {{-- Sin id, solo onclick --}}
            <button onclick="abrirModal()" class="btn-new">
                <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                        d="M12 4v16m8-8H4" />
                </svg>
                Nuevo proyecto
            </button>
            <a href="{{ route('profile.index') }}">
                <img src="{{ $profile->avatar ?? '' }}" alt="Tú" class="nav-user-av" />
            </a>
            @endif
        </div>
    </div>
</nav>
@if(Auth::user()->role != 'guest')
<x-new-project />
@endif

@push('styles')
<link rel="stylesheet" href="{{ asset('css/topbar.css') }}">
@endpush