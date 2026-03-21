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
        <button class="btn-avatar-action danger" id="btnDelete">Eliminar foto</button>
    </div>
</aside>
@include('components.modalImage')
@push('styles')
<link rel="stylesheet" href="{{ asset('css/profile/sidebar.css') }}">
<link rel="stylesheet" href="{{ asset('css/profile/modalImage.css') }}">
@endpush
@push('scripts')
<script src="{{ asset('js/profile/avatar.js') }}"></script>
@endpush