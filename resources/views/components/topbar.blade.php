@props(['profile'])

<!-- ══════════════════════════════════════════
     NAVBAR
═════════════════════════════════════════ -->
@php
    $unreadMessages = Auth::user()->role !== 'guest' 
        ? \App\Models\Conversation::getUnreadGlobalCount() 
        : 0;
    $unreadNotifications = Auth::user()->role !== 'guest'
        ? \App\Models\Notification::unreadCount(Auth::id())
        : 0;
@endphp

<nav class="navbar" role="navigation" aria-label="Navegación principal">
    <div class="navbar-inner">

        {{-- ── Izquierda: logo + links + búsqueda ── --}}
        <div class="navbar-left">
            <a href="{{ route('explore.index') }}" aria-label="Ir a inicio">
                <img src="{{ asset('img/logoFluxa.png') }}" alt="Fluxa" class="nav-logo" />
            </a>

            <nav class="nav-links" aria-label="Links principales">
                @if(Auth::user()->role !== 'guest')
                <a href="{{ route('feed.index') }}"
                    class="nav-link {{ request()->routeIs('feed*') ? 'active' : '' }}"
                    @if(request()->routeIs('feed*')) aria-current="page" @endif>
                    Feed
                </a>
                @endif
                <a href="{{ route('explore.index') }}"
                    class="nav-link {{ request()->routeIs('explore*') ? 'active' : '' }}"
                    @if(request()->routeIs('explore*')) aria-current="page" @endif>
                    Explorar
                </a>
                @if(Auth::user()->role !== 'guest')
                <a href="{{ route('notifications.index') }}"
                    class="nav-link {{ request()->routeIs('notifications*') ? 'active' : '' }}"
                    @if(request()->routeIs('notifications*')) aria-current="page" @endif>
                    Notificaciones
                    <span class="nav-badge" id="navNotificationsBadge" style="{{ $unreadNotifications > 0 ? '' : 'display: none' }}">{{ $unreadNotifications > 99 ? '99+' : $unreadNotifications }}</span>
                </a>
                <a href="{{ route('suggestions.create') }}"
                    class="nav-link {{ request()->routeIs('suggestions*') ? 'active' : '' }}"
                    @if(request()->routeIs('suggestions*')) aria-current="page" @endif>
                    Sugerencias
                </a>
                <a href="{{ route('messages.index') }}"
                    class="nav-link {{ request()->routeIs('messages*') ? 'active' : '' }}"
                    @if(request()->routeIs('messages*')) aria-current="page" @endif>
                    Mensajes
                    <span class="nav-badge" id="navMessagesBadge" style="{{ $unreadMessages > 0 ? '' : 'display: none' }}">{{ $unreadMessages > 99 ? '99+' : $unreadMessages }}</span>
                </a>
                @if(Auth::user()->role === 'admin')
                <a href="{{ route('admin.suggestions.index') }}"
                    class="nav-link {{ request()->routeIs('admin.suggestions*') ? 'active' : '' }}"
                    @if(request()->routeIs('admin.suggestions*')) aria-current="page" @endif>
                    Admin
                </a>
                @endif
                @endif
            </nav>

            <div class="nav-search-wrap">
                <svg class="nav-search-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <input type="search"
                    class="nav-search"
                    placeholder="Buscar proyectos, usuarios..."
                    id="globalSearch"
                    autocomplete="off"
                    aria-label="Buscar en Fluxa">
                <div id="searchResults" class="search-results" role="listbox" aria-live="polite"></div>
            </div>
        </div>

        {{-- ── Derecha: acciones + avatar + hamburguesa ── --}}
        <div class="navbar-right">
            @if(Auth::user()->role === 'guest')
            <form action="{{ route('guest.destroy') }}" method="POST" class="nav-guest-form">
                @csrf
                <button type="submit" class="btn-new danger">Salir</button>
            </form>
            @else
            <button onclick="abrirModal()" class="btn-new" aria-label="Crear nuevo proyecto">
                <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                        d="M12 4v16m8-8H4" />
                </svg>
                <span>Nuevo proyecto</span>
            </button>
            <a href="{{ route('profile.index') }}" aria-label="Ver mi perfil">
                <img src="{{ Auth::user()->avatar_url }}" alt="Tu perfil" class="nav-user-av" />
            </a>
            @endif

            <button
                class="mobile-menu-btn"
                id="mobileMenuBtn"
                onclick="toggleMobileMenu()"
                aria-label="Abrir menú"
                aria-expanded="false"
                aria-controls="mobileMenu">
                <svg class="icon-hamburger" width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
                <svg class="icon-close" width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    </div>
</nav>

{{-- Modal nuevo proyecto (solo usuarios registrados) --}}
@if(Auth::user()->role !== 'guest')
<x-new-project />
@endif

{{-- ── Menú móvil ── --}}
<div class="mobile-menu" id="mobileMenu" role="dialog" aria-label="Menú de navegación" aria-hidden="true">
    <div class="mobile-menu-content">

        {{-- Búsqueda móvil --}}
        <div class="mobile-search-wrap">
            <svg class="mobile-search-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
            <input type="search"
                class="mobile-search"
                placeholder="Buscar proyectos, usuarios..."
                id="mobileSearch"
                autocomplete="off"
                aria-label="Buscar en Fluxa">
        </div>

        {{-- Links de navegación --}}
        @if(Auth::user()->role !== 'guest')
        <a href="{{ route('feed.index') }}"
            class="mobile-menu-link {{ request()->routeIs('feed*') ? 'active' : '' }}"
            @if(request()->routeIs('feed*')) aria-current="page" @endif>
            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
            </svg>
            Feed
        </a>
        @endif

        <a href="{{ route('explore.index') }}"
            class="mobile-menu-link {{ request()->routeIs('explore*') ? 'active' : '' }}"
            @if(request()->routeIs('explore*')) aria-current="page" @endif>
            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
            Explorar
        </a>

        @if(Auth::user()->role !== 'guest')
        <a href="{{ route('notifications.index') }}"
            class="mobile-menu-link {{ request()->routeIs('notifications*') ? 'active' : '' }}"
            @if(request()->routeIs('notifications*')) aria-current="page" @endif>
            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
            </svg>
            Notificaciones
            @if($unreadNotifications > 0)
            <span class="mobile-badge">{{ $unreadNotifications > 99 ? '99+' : $unreadNotifications }}</span>
            @endif
        </a>

        <a href="{{ route('messages.index') }}"
            class="mobile-menu-link {{ request()->routeIs('messages*') ? 'active' : '' }}"
            @if(request()->routeIs('messages*')) aria-current="page" @endif>
            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
            </svg>
            Mensajes
        </a>

        @if(Auth::user()->role === 'admin')
        <a href="{{ route('admin.suggestions.index') }}"
            class="mobile-menu-link {{ request()->routeIs('admin.suggestions*') ? 'active' : '' }}"
            @if(request()->routeIs('admin.suggestions*')) aria-current="page" @endif>
            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
            Admin
        </a>
        @endif

        <a href="{{ route('profile.index') }}"
            class="mobile-menu-link {{ request()->routeIs('profile*') ? 'active' : '' }}"
            @if(request()->routeIs('profile*')) aria-current="page" @endif>
            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
            </svg>
            Mi perfil
        </a>

        <div class="mobile-menu-divider"></div>

        <button
            class="mobile-menu-link mobile-menu-action"
            onclick="closeMobileMenuAndOpen()">
            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Nuevo proyecto
        </button>

        @else
        {{-- Guest: mostrar opción de salir en el menú móvil --}}
        <div class="mobile-menu-divider"></div>
        <form action="{{ route('guest.destroy') }}" method="POST">
            @csrf
            <button type="submit" class="mobile-menu-link mobile-menu-danger">
                <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                </svg>
                Salir de la sesión de invitado
            </button>
        </form>
        @endif
    </div>
</div>

@push('scripts')
@vite('resources/js/shared/topbar.js')
@endpush
@push('styles')
@vite('resources/css/shared/topbar.css')
@endpush