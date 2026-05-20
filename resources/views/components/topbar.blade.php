@props(['profile'])

<!-- ══════════════════════════════════════════
     NAVBAR — $unreadMessages y $unreadNotifications
     son inyectados por TopbarComposer
═════════════════════════════════════════ -->

<nav class="navbar" role="navigation" aria-label="Navegación principal">
    <div class="navbar-inner">

        {{-- ── Izquierda: logo + links + búsqueda ── --}}
        <div class="navbar-left">
            <a href="{{ route('explore.index') }}" aria-label="Ir a inicio">
                <img src="{{ asset('img/logoFluxa.png') }}" alt="Fluxa" class="nav-logo" />
            </a>

            <nav class="nav-links" aria-label="Links principales">
                @if(Auth::user()->role !== 'guest')
                <div class="nav-dropdown">
                    <button class="nav-dropdown-trigger {{ request()->routeIs('feed*') || request()->routeIs('explore*') ? 'active' : '' }}"
                        id="feedDropdownBtn"
                        aria-haspopup="true"
                        aria-expanded="false"
                        onclick="toggleFeedDropdown(event)">
                        Descubrir
                        <svg class="nav-dropdown-chevron" width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div class="nav-dropdown-menu" id="feedDropdownMenu" role="menu" aria-labelledby="feedDropdownBtn">
                        <a href="{{ route('feed.index') }}"
                            class="nav-dropdown-item {{ request()->routeIs('feed*') ? 'active' : '' }}"
                            @if(request()->routeIs('feed*')) aria-current="page" @endif
                            role="menuitem">
                            Feed
                        </a>
                        <a href="{{ route('explore.index') }}"
                            class="nav-dropdown-item {{ request()->routeIs('explore*') ? 'active' : '' }}"
                            @if(request()->routeIs('explore*')) aria-current="page" @endif
                            role="menuitem">
                            Explorar
                        </a>
                    </div>
                </div>
                <a href="{{ route('diary.index') }}"
                    class="nav-link {{ request()->routeIs('diary*') ? 'active' : '' }}"
                    @if(request()->routeIs('diary*')) aria-current="page" @endif>
                    Diario
                </a>
                @endif
                <!-- <div class="nav-dropdown">
                    <button class="nav-dropdown-trigger {{ request()->routeIs('salaries*') || request()->routeIs('jobs*') ? 'active' : '' }}"
                        id="jobsDropdownBtn"
                        aria-haspopup="true"
                        aria-expanded="false"
                        onclick="toggleJobsDropdown(event)">
                        Oportunidades
                        <svg class="nav-dropdown-chevron" width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div class="nav-dropdown-menu" id="jobsDropdownMenu" role="menu" aria-labelledby="jobsDropdownBtn">
                        <a href="{{ route('salaries.index') }}"
                            class="nav-dropdown-item {{ request()->routeIs('salaries*') ? 'active' : '' }}"
                            @if(request()->routeIs('salaries*')) aria-current="page" @endif
                            role="menuitem">
                            Sueldos
                        </a>
                        @if(Auth::user()->role !== 'guest')
                        <a href="{{ route('jobs.index') }}"
                            class="nav-dropdown-item {{ request()->routeIs('jobs*') ? 'active' : '' }}"
                            @if(request()->routeIs('jobs*')) aria-current="page" @endif
                            role="menuitem">
                            Bolsa de empleo
                        </a>
                        @endif
                    </div>
                </div> -->
                @if(Auth::user()->role !== 'guest')
                <a href="{{ route('notifications.index') }}"
                    class="nav-link {{ request()->routeIs('notifications*') ? 'active' : '' }}"
                    @if(request()->routeIs('notifications*')) aria-current="page" @endif>
                    Notificaciones
                    <span class="nav-badge" id="navNotificationsBadge" style="{{ $unreadNotifications > 0 ? '' : 'display: none' }}">{{ $unreadNotifications > 99 ? '99+' : $unreadNotifications }}</span>
                </a>
                <a href="{{ route('messages.index') }}"
                    class="nav-link {{ request()->routeIs('messages*') ? 'active' : '' }}"
                    @if(request()->routeIs('messages*')) aria-current="page" @endif>
                    Mensajes
                    <span class="nav-badge" id="navMessagesBadge" style="{{ $unreadMessages > 0 ? '' : 'display: none' }}">{{ $unreadMessages > 99 ? '99+' : $unreadMessages }}</span>
                </a>
                @if(Auth::user()->role === 'admin')
                <a href="{{ route('admin.dashboard') }}"
                    class="nav-link {{ request()->routeIs('admin.dashboard*') ? 'active' : '' }}"
                    @if(request()->routeIs('admin.dashboard*')) aria-current="page" @endif>
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
                    placeholder="Buscar..."
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
            @elseif(Auth::user()->account_type === 'company')
            <button onclick="abrirJobOffer()" class="btn-new" aria-label="Publicar oferta de empleo">
                <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
                <span>Publicar oferta</span>
            </button>
            @else
            <button onclick="abrirModal()" class="btn-new" aria-label="Crear nuevo proyecto">
                <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                        d="M12 4v16m8-8H4" />
                </svg>
                <span>Nuevo proyecto</span>
            </button>
            @endif
            @if(Auth::user()->role !== 'guest')
            <div class="nav-dropdown nav-help-dropdown">
                <button class="nav-help-btn"
                    id="helpDropdownBtn"
                    aria-haspopup="true"
                    aria-expanded="false"
                    onclick="toggleHelpDropdown(event)"
                    aria-label="Ayuda">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <circle cx="12" cy="12" r="10"/>
                        <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/>
                        <line x1="12" y1="17" x2="12.01" y2="17"/>
                    </svg>
                </button>
                <div class="nav-dropdown-menu" id="helpDropdownMenu" role="menu" aria-labelledby="helpDropdownBtn">
                    <a href="{{ route('suggestions.create') }}"
                        class="nav-dropdown-item {{ request()->routeIs('suggestions*') ? 'active' : '' }}"
                        @if(request()->routeIs('suggestions*')) aria-current="page" @endif
                        role="menuitem">
                        Sugerencias
                    </a>
                    <button type="button"
                        class="nav-dropdown-item"
                        onclick="abrirReportProblemModal()"
                        role="menuitem">
                        Reportar problema
                    </button>
                </div>
            </div>
            @endif
            <a href="{{ route('profile.index') }}" aria-label="Ver mi perfil">
                <img src="{{ Auth::user()->avatar_url }}" alt="Tu perfil" class="nav-user-av" />
            </a>

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
@if(Auth::user()->account_type === 'company')
<x-new-job-offer />
@endif
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
        <div class="mobile-search-results" id="mobileSearchResults"></div>

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
        <a href="{{ route('diary.index') }}"
            class="mobile-menu-link {{ request()->routeIs('diary*') ? 'active' : '' }}"
            @if(request()->routeIs('diary*')) aria-current="page" @endif>
            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <rect x="3" y="4" width="18" height="18" rx="2" ry="2" />
                <line x1="16" y1="2" x2="16" y2="6" />
                <line x1="8" y1="2" x2="8" y2="6" />
                <line x1="3" y1="10" x2="21" y2="10" />
            </svg>
            Diario
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
        <a href="{{ route('jobs.index') }}"
            class="mobile-menu-link {{ request()->routeIs('jobs*') ? 'active' : '' }}"
            @if(request()->routeIs('jobs*')) aria-current="page" @endif>
            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
            </svg>
            Bolsa de empleo
        </a>
        @endif

        <a href="{{ route('salaries.index') }}"
            class="mobile-menu-link {{ request()->routeIs('salaries*') ? 'active' : '' }}"
            @if(request()->routeIs('salaries*')) aria-current="page" @endif>
            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            Sueldos
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
        <a href="{{ route('admin.dashboard') }}"
            class="mobile-menu-link {{ request()->routeIs('admin.*') ? 'active' : '' }}"
            @if(request()->routeIs('admin.*')) aria-current="page" @endif>
            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <rect x="3" y="3" width="7" height="7" rx="1" />
                <rect x="14" y="3" width="7" height="7" rx="1" />
                <rect x="14" y="14" width="7" height="7" rx="1" />
                <rect x="3" y="14" width="7" height="7" rx="1" />
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

        <a href="{{ route('about-fluxa') }}"
            class="mobile-menu-link {{ request()->routeIs('about-fluxa*') ? 'active' : '' }}"
            @if(request()->routeIs('about-fluxa*')) aria-current="page" @endif>
            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path d="M10 8l-3-4M14 8l3-4" stroke-linecap="round" stroke-width="2" />
                <circle cx="12" cy="10" r="2" stroke-width="2" />
                <circle cx="12" cy="14.5" r="2.5" stroke-width="2" />
                <ellipse cx="12" cy="19.5" rx="3.5" ry="2.5" stroke-width="2" />
                <path d="M9.5 10l-3 1.5M14.5 10l3 1.5" stroke-linecap="round" stroke-width="2" />
                <path d="M8.5 14.5l-3.5 1.5M15.5 14.5l3.5 1.5" stroke-linecap="round" stroke-width="2" />
                <path d="M9 18.5l-2 2.5M15 18.5l2 2.5" stroke-linecap="round" stroke-width="2" />
            </svg>
            Sobre Fluxa
        </a>

        <a href="{{ route('suggestions.create') }}"
            class="mobile-menu-link {{ request()->routeIs('suggestions*') ? 'active' : '' }}"
            @if(request()->routeIs('suggestions*')) aria-current="page" @endif>
            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
            </svg>
            Sugerencias
        </a>

        <button
            class="mobile-menu-link"
            onclick="abrirReportProblemModal()">
            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            Reportar problema
        </button>

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

{{-- Modal reportar problema --}}
<x-modal-report-problem />

@push('scripts')
@vite('resources/js/shared/topbar.js')
@vite('resources/js/shared/reportProblem.js')
@endpush
@push('styles')
@vite('resources/css/shared/topbar.css')
@vite('resources/css/shared/modal.css')
@endpush