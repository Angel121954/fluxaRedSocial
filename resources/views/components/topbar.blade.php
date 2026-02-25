<!-- ══════════════════════════════════════════
     NAVBAR
══════════════════════════════════════════ -->
<nav class="navbar">
    <div class="navbar-inner">
        <div class="navbar-left">
            <a href="{{ route('explore.index') }}">
                <img src="{{ asset('img/logoFluxa.png') }}" alt="Logo de Fluxa en forma de hormiga tech" class="nav-logo" />
            </a>
            <nav class="nav-links">
                <a href="#"
                    class="nav-link {{ request()->routeIs('feed*') ? 'active' : '' }}">
                    Feed
                </a>
                <a href="{{ route('explore.index') }}"
                    class="nav-link {{ request()->routeIs('explore*') ? 'active' : '' }}">
                    Explorar
                </a>
                <a href="{{ route('notifications.index') }}"
                    class="nav-link {{ request()->routeIs('notifications*') ? 'active' : '' }}">
                    Notificaciones
                </a>
            </nav>
        </div>

        @if( request()->routeIs('explore*') || request()->routeIs('feed*') )
        <div class="nav-search-wrap">
            <svg class="nav-search-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
            <input type="text" class="nav-search" placeholder="Buscar en Fluxa..." />
        </div>
        @endif

        <div class="navbar-right">
            <button class="btn-new">
                <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                        d="M12 4v16m8-8H4" />
                </svg>
                Nuevo proyecto
            </button>
            <a href="{{ route('profile.index') }}">
                <img src="{{ Auth::user()->avatar }}"
                    alt="Tú"
                    class="nav-user-av" />
            </a>
        </div>
    </div>
</nav>
@push('styles')
<link rel="stylesheet" href="{{ asset('css/topbar.css') }}">
@endpush