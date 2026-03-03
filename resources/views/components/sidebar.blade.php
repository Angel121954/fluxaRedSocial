<!-- ──── SIDEBAR ──── -->
<aside class="sidebar">
    <div class="sidebar-nav">
        <a href="{{ route('configuration.index') }}">
            <div class="sidebar-item {{ request()->routeIs('configuration*') ? 'active' : '' }}">Editar perfil
            </div>
        </a>
        <a href="{{ route('account.index') }}">
            <div class="sidebar-item {{ request()->routeIs('account*') ? 'active' : '' }}">Cuenta</div>
        </a>
        <a href="{{ route('security.index') }}">
            <div class="sidebar-item {{ request()->routeIs('security*') ? 'active' : '' }}">Seguridad</div>
        </a>
        <a href="{{ route('notification-preference.index') }}">
            <div class="sidebar-item {{ request()->routeIs('notification-preference*') ? 'active' : '' }}">Preferencias</div>
        </a>
        <div class="sidebar-item">Privacidad</div>
    </div>

    <!-- Avatar section -->
    <div class="avatar-section">
        <div class="avatar-title">Foto de perfil</div>

        <div class="avatar-container" id="avatarContainer">
            <img
                src="{{ $profile->avatar ?? '' }}"
                alt="Profile"
                class="avatar-img"
                id="avatarImg" />
            <div class="avatar-overlay">
                <button class="avatar-btn" id="btnUpload" title="Cambiar foto">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </button>
                <button class="avatar-btn" id="btnView" title="Ver foto">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                </button>
            </div>
        </div>

        <button class="btn-avatar-action" id="btnChange">Cambiar foto</button>
        <button class="btn-avatar-action danger" id="btnDelete">
            Eliminar foto
        </button>
    </div>
</aside>
@push('styles')
<link rel="stylesheet" href="{{ asset('css/sidebar.css') }}">
@endpush