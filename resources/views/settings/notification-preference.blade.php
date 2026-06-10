@extends('layouts.app')
@section('title', 'Preferencias de notificaciones')
@section('content')
<x-topbar :profile="$profile" />

<div class="edit-layout">
    <x-sidebar :profile="$profile" />

    <main class="main-content">
        <h1 class="page-title">Preferencias de notificaciones</h1>
        <p class="page-subtitle">Administra cómo y cuándo quieres recibir <strong>notificaciones</strong>.</p>

        <form method="POST" action="{{ route('notification-preference.update') }}" id="formNotifications">
            @csrf
            @method('PATCH')

            {{-- ── Canales ──────────────────────────────────────────── --}}
            <div class="notif-section">
                <div class="notif-section__content">
                    <h2 class="notif-section__title">Configurar notificaciones</h2>

                    <div class="notif-toggle-row">
                        <div class="notif-toggle-info">
                            <span class="notif-toggle-label">Notificaciones por email</span>
                            <span class="notif-toggle-desc">Recibe notificaciones de Fluxa por <strong>correo electrónico</strong></span>
                        </div>
                        <label class="notif-switch" aria-label="Activar notificaciones por email">
                            <input type="checkbox" name="email_enabled" id="toggleEmail"
                                {{ old('email_enabled', auth()->user()?->notificationPreferences?->email_enabled ?? true) ? 'checked' : '' }} />
                            <span class="notif-track"></span>
                        </label>
                    </div>

                    <div class="notif-toggle-row">
                        <div class="notif-toggle-info">
                            <span class="notif-toggle-label">Notificaciones push</span>
                            <span class="notif-toggle-desc">Recibe notificaciones directas en tu <strong>dispositivo</strong></span>
                        </div>
                        <label class="notif-switch" aria-label="Activar notificaciones push">
                            <input type="checkbox" name="push_enabled" id="togglePush"
                                {{ old('push_enabled', auth()->user()?->notificationPreferences?->push_enabled ?? true) ? 'checked' : '' }} />
                            <span class="notif-track"></span>
                        </label>
                    </div>
                </div>


            </div>

            <div class="notif-divider"></div>

            {{-- ── Tipos ────────────────────────────────────────────── --}}
            <div class="notif-types">
                <h2 class="notif-section__title">Tipos de notificaciones</h2>

                <div class="notif-check-list">

                    <label class="notif-check-item">
                        <input type="checkbox" name="notify_comments" class="notif-checkbox"
                            {{ old('notify_comments', auth()->user()?->notificationPreferences?->notify_comments ?? true) ? 'checked' : '' }} />
                        <span class="notif-check-custom">
                            <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                <polyline points="20 6 9 17 4 12" />
                            </svg>
                        </span>
                        <span class="notif-check-text">Alguien comenta en mis <strong>publicaciones</strong></span>
                    </label>

                    <label class="notif-check-item">
                        <input type="checkbox" name="notify_followers" class="notif-checkbox"
                            {{ old('notify_followers', auth()->user()?->notificationPreferences?->notify_followers ?? true) ? 'checked' : '' }} />
                        <span class="notif-check-custom">
                            <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                <polyline points="20 6 9 17 4 12" />
                            </svg>
                        </span>
                        <span class="notif-check-text">Alguien comienza a <strong>seguirme</strong></span>
                    </label>

                    <label class="notif-check-item">
                        <input type="checkbox" name="notify_mentions" class="notif-checkbox"
                            {{ old('notify_mentions', auth()->user()?->notificationPreferences?->notify_mentions ?? true) ? 'checked' : '' }} />
                        <span class="notif-check-custom">
                            <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                <polyline points="20 6 9 17 4 12" />
                            </svg>
                        </span>
                        <span class="notif-check-text">Me mencionan en una <strong>publicación</strong></span>
                    </label>

                    <label class="notif-check-item notif-check-item--with-desc">
                        <input type="checkbox" name="weekly_summary" class="notif-checkbox"
                            {{ old('weekly_summary', auth()->user()?->notificationPreferences?->weekly_summary ?? true) ? 'checked' : '' }} />
                        <span class="notif-check-custom">
                            <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                <polyline points="20 6 9 17 4 12" />
                            </svg>
                        </span>
                        <div class="notif-check-block">
                            <span class="notif-check-text">Recibir un resumen semanal</span>
                            <span class="notif-check-hint">Recibe un email semanal con un resumen de lo <strong>más importante</strong>.</span>
                        </div>
                    </label>

                </div>
            </div>

            <div class="notif-actions">
                <x-btn-cancel />
                <x-btn-submit>
                    Guardar cambios
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                        <polyline points="9 18 15 12 9 6" />
                    </svg>
                </x-btn-submit>
            </div>

        </form>
    </main>
</div>

@endsection

@push('styles')
@vite('resources/css/profile/shared.css')
@vite('resources/css/profile/sidebar.css')
@vite('resources/css/settings/preferences.css')
@endpush

@push('scripts')
@vite('resources/js/profile/avatar.js')
@endpush