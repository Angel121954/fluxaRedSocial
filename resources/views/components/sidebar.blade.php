@props(['profile'])

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
        <a href="{{ route('privacy.index') }}">
            <div class="sidebar-item {{ request()->routeIs('privacy*') ? 'active' : '' }}">Privacidad</div>
        </a>
        <a href="{{ route('work-experiences.index') }}">
            <div class="sidebar-item {{ request()->routeIs('work-experiences*') ? 'active' : '' }}">Experiencia Laboral</div>
        </a>
        <a href="{{ route('educations.index') }}">
            <div class="sidebar-item {{ request()->routeIs('educations*') ? 'active' : '' }}">Educación</div>
        </a>
    </div>

    <!-- Avatar section -->
    <div class="avatar-section">
        <div class="avatar-title">Foto de perfil</div>

        <div class="avatar-container" id="avatarWrap">
            <img
                src="{{ $profile->avatar ?? '' }}"
                alt="Profile"
                class="avatar-img btnView"
                id="avatarImg" />
        </div>

        <input type="file" id="fileIn" accept="image/*" hidden>
        <button class="btn-avatar-action btnCam" id="btnChange">Cambiar foto</button>
        <button data-username="{{ Auth()->user()->username }}" class="btn-avatar-action danger" id="btnDelete">Eliminar foto</button>
    </div>
</aside>
<x-modal-image />

@push('scripts')
@vite('resources/js/shared/topbar.js')
@endpush