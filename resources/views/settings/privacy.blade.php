@extends('layouts.app')
@section('title', 'Privacidad')
@section('content')
<x-topbar :profile="$profile" />

<div class="edit-layout">
    <x-sidebar :profile="$profile" />

    <main class="main-content">
        <h1 class="page-title">Privacidad</h1>
        <p class="page-subtitle">Gestiona la privacidad y protección de tu cuenta</p>

        <form action="{{ route('privacy.update') }}" method="POST" id="privacyForm">
            @csrf
            @method('PATCH')

            <div class="toggle-group">

                <div class="toggle-row">
                    <div class="toggle-info">
                        <span class="toggle-label">Perfil privado</span>
                        <span class="toggle-desc">Haz que tu perfil sea privado para que solo tus seguidores puedan ver tu perfil y proyectos.</span>
                    </div>
                    <label class="toggle-switch">
                        <input type="checkbox" name="visibility" value="private"
                            {{ old('visibility', $profile->visibility ?? 'public') === 'private' ? 'checked' : '' }}>
                        <span class="toggle-track">
                            <span class="toggle-thumb"></span>
                        </span>
                    </label>
                </div>

                <div class="toggle-row">
                    <div class="toggle-info">
                        <span class="toggle-label">Aceptar mensajes directos</span>
                        <span class="toggle-desc">Permitir que otros usuarios te envíen mensajes directos.</span>
                    </div>
                    <label class="toggle-switch">
                        <input type="checkbox" name="accept_messages" value="1"
                            {{ old('accept_messages', $profile->accept_messages ?? true) ? 'checked' : '' }}>
                        <span class="toggle-track">
                            <span class="toggle-thumb"></span>
                        </span>
                    </label>
                </div>

                <div class="toggle-row">
                    <div class="toggle-info">
                        <span class="toggle-label">Mostrar el correo electrónico en el perfil</span>
                        <span class="toggle-desc">Permitir que tu dirección de correo electrónico sea visible en tu perfil.</span>
                    </div>
                    <label class="toggle-switch">
                        <input type="checkbox" name="show_email" value="1"
                            {{ old('show_email', $profile->show_email ?? false) ? 'checked' : '' }}>
                        <span class="toggle-track">
                            <span class="toggle-thumb"></span>
                        </span>
                    </label>
                </div>

                <div class="toggle-row">
                    <div class="toggle-info">
                        <span class="toggle-label">Mostrar proyectos favoritos</span>
                        <span class="toggle-desc">Permitir que otros usuarios puedan ver tus proyectos favoritos.</span>
                    </div>
                    <label class="toggle-switch">
                        <input type="checkbox" name="show_bookmarks" value="1"
                            {{ old('show_bookmarks', $profile->show_bookmarks ?? false) ? 'checked' : '' }}>
                        <span class="toggle-track">
                            <span class="toggle-thumb"></span>
                        </span>
                    </label>
                </div>

            </div>

            <div class="form-actions">
                <x-btn-cancel href="{{ route('profile.index') }}" />
                <x-btn-submit>Guardar cambios</x-btn-submit>
            </div>

        </form>
    </main>
</div>

@endsection

@push('styles')
@vite('resources/css/profile/shared.css')
@vite('resources/css/profile/sidebar.css')
@vite('resources/css/settings/privacy.css')
@endpush

@push('scripts')
@vite('resources/js/profile/avatar.js')
@endpush