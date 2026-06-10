@props([
'unreadNotifications' => 0,
'unreadMessages' => 0,
])

<nav class="bottom-nav" role="navigation" aria-label="Navegación móvil">
    <a href="{{ route('feed.index') }}"
        class="bottom-nav-item {{ request()->routeIs('feed*') ? 'active' : '' }}"
        @if(request()->routeIs('feed*')) aria-current="page" @endif>
        <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
        </svg>
        <span class="bottom-nav-label">Feed</span>
    </a>

    <a href="{{ route('explore.index') }}"
        class="bottom-nav-item {{ request()->routeIs('explore*') ? 'active' : '' }}"
        @if(request()->routeIs('explore*')) aria-current="page" @endif>
        <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
        </svg>
        <span class="bottom-nav-label">Explorar</span>
    </a>

    <a href="{{ route('notifications.index') }}"
        class="bottom-nav-item {{ request()->routeIs('notifications*') ? 'active' : '' }}"
        @if(request()->routeIs('notifications*')) aria-current="page" @endif>
        <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
        </svg>
        <span class="bottom-nav-label">Notif.</span>
        <span class="bottom-nav-badge" id="bottomNavNotifBadge"
            style="{{ $unreadNotifications > 0 ? '' : 'display: none' }}">
            {{ $unreadNotifications > 99 ? '99+' : $unreadNotifications }}
        </span>
    </a>

    <a href="{{ route('messages.index') }}"
        class="bottom-nav-item {{ request()->routeIs('messages*') ? 'active' : '' }}"
        @if(request()->routeIs('messages*')) aria-current="page" @endif>
        <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
        </svg>
        <span class="bottom-nav-label">Mensajes</span>
        <span class="bottom-nav-badge" id="bottomNavMsgBadge"
            style="{{ $unreadMessages > 0 ? '' : 'display: none' }}">
            {{ $unreadMessages > 99 ? '99+' : $unreadMessages }}
        </span>
    </a>

    <a href="{{ route('profile.index') }}"
        class="bottom-nav-item {{ request()->routeIs('profile.index') ? 'active' : '' }}"
        @if(request()->routeIs('profile.index')) aria-current="page" @endif>
        <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
        </svg>
        <span class="bottom-nav-label">Perfil</span>
    </a>

</nav>

{{-- ── FAB: Nuevo proyecto (mobile) ── --}}
@if(Auth::user()->role !== 'guest')
<button class="bottom-fab" onclick="abrirModal()" aria-label="Crear nuevo proyecto" title="Nuevo proyecto">
    <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
    </svg>
</button>
@endif