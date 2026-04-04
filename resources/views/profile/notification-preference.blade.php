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
                                {{ old('email_enabled', auth()->user()->notificationPreferences->email_enabled ?? true) ? 'checked' : '' }} />
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
                                {{ old('push_enabled', auth()->user()->notificationPreferences->push_enabled ?? true) ? 'checked' : '' }} />
                            <span class="notif-track"></span>
                        </label>
                    </div>
                </div>

                {{-- Ilustración --}}
                <div class="notif-illus" aria-hidden="true">
                    <div class="illus-notif-phone">
                        <div class="illus-notif-screen">
                            <div class="illus-notif-bar"></div>
                            <div class="illus-notif-bar illus-notif-bar--sm"></div>
                        </div>
                    </div>
                    <div class="illus-bell illus-bell--lg">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 22c1.1 0 2-.9 2-2h-4c0 1.1.9 2 2 2zm6-6v-5c0-3.07-1.64-5.64-4.5-6.32V4c0-.83-.67-1.5-1.5-1.5s-1.5.67-1.5 1.5v.68C7.63 5.36 6 7.92 6 11v5l-2 2v1h16v-1l-2-2z" />
                        </svg>
                        <span class="illus-bell__badge">1</span>
                    </div>
                    <div class="illus-bell illus-bell--sm">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 22c1.1 0 2-.9 2-2h-4c0 1.1.9 2 2 2zm6-6v-5c0-3.07-1.64-5.64-4.5-6.32V4c0-.83-.67-1.5-1.5-1.5s-1.5.67-1.5 1.5v.68C7.63 5.36 6 7.92 6 11v5l-2 2v1h16v-1l-2-2z" />
                        </svg>
                    </div>
                    <div class="illus-gear">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M19.14,12.94c0.04-0.3,0.06-0.61,0.06-0.94c0-0.32-0.02-0.64-0.07-0.94l2.03-1.58c0.18-0.14,0.23-0.41,0.12-0.61 l-1.92-3.32c-0.12-0.22-0.37-0.29-0.59-0.22l-2.39,0.96c-0.5-0.38-1.03-0.7-1.62-0.94L14.4,2.81c-0.04-0.24-0.24-0.41-0.48-0.41 h-3.84c-0.24,0-0.43,0.17-0.47,0.41L9.25,5.35C8.66,5.59,8.12,5.92,7.63,6.29L5.24,5.33c-0.22-0.08-0.47,0-0.59,0.22L2.74,8.87 C2.62,9.08,2.66,9.34,2.86,9.48l2.03,1.58C4.84,11.36,4.8,11.69,4.8,12s0.02,0.64,0.07,0.94l-2.03,1.58 c-0.18,0.14-0.23,0.41-0.12,0.61l1.92,3.32c0.12,0.22,0.37,0.29,0.59,0.22l2.39-0.96c0.5,0.38,1.03,0.7,1.62,0.94l0.36,2.54 c0.05,0.24,0.24,0.41,0.48,0.41h3.84c0.24,0,0.44-0.17,0.47-0.41l0.36-2.54c0.59-0.24,1.13-0.56,1.62-0.94l2.39,0.96 c0.22,0.08,0.47,0,0.59-0.22l1.92-3.32c0.12-0.22,0.07-0.47-0.12-0.61L19.14,12.94z M12,15.6c-1.98,0-3.6-1.62-3.6-3.6 s1.62-3.6,3.6-3.6s3.6,1.62,3.6,3.6S13.98,15.6,12,15.6z" />
                        </svg>
                    </div>
                    <div class="illus-envelope">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z" />
                        </svg>
                    </div>
                    <div class="illus-spark illus-spark--1"></div>
                    <div class="illus-spark illus-spark--2"></div>
                    <div class="illus-spark illus-spark--3"></div>
                    <div class="illus-spark illus-spark--4"></div>
                </div>
            </div>

            <div class="notif-divider"></div>

            {{-- ── Tipos ────────────────────────────────────────────── --}}
            <div class="notif-types">
                <h2 class="notif-section__title">Tipos de notificaciones</h2>

                <div class="notif-check-list">

                    <label class="notif-check-item">
                        <input type="checkbox" name="notify_comments" class="notif-checkbox"
                            {{ old('notify_comments', auth()->user()->notificationPreferences->notify_comments ?? true) ? 'checked' : '' }} />
                        <span class="notif-check-custom">
                            <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                <polyline points="20 6 9 17 4 12" />
                            </svg>
                        </span>
                        <span class="notif-check-text">Alguien comenta en mis <strong>publicaciones</strong></span>
                    </label>

                    <label class="notif-check-item">
                        <input type="checkbox" name="notify_followers" class="notif-checkbox"
                            {{ old('notify_followers', auth()->user()->notificationPreferences->notify_followers ?? true) ? 'checked' : '' }} />
                        <span class="notif-check-custom">
                            <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                <polyline points="20 6 9 17 4 12" />
                            </svg>
                        </span>
                        <span class="notif-check-text">Alguien comienza a <strong>seguirme</strong></span>
                    </label>

                    <label class="notif-check-item">
                        <input type="checkbox" name="notify_mentions" class="notif-checkbox"
                            {{ old('notify_mentions', auth()->user()->notificationPreferences->notify_mentions ?? true) ? 'checked' : '' }} />
                        <span class="notif-check-custom">
                            <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                <polyline points="20 6 9 17 4 12" />
                            </svg>
                        </span>
                        <span class="notif-check-text">Me mencionan en una <strong>publicación</strong></span>
                    </label>

                    <label class="notif-check-item notif-check-item--with-desc">
                        <input type="checkbox" name="weekly_summary" class="notif-checkbox"
                            {{ old('weekly_summary', auth()->user()->notificationPreferences->weekly_summary ?? true) ? 'checked' : '' }} />
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

            <x-alert />

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
<link rel="stylesheet" href="{{ asset('css/profile/shared.css') }}" />
<link rel="stylesheet" href="{{ asset('css/profile/sidebar.css') }}" />
<link rel="stylesheet" href="{{ asset('css/profile/preferences.css') }}" />
@endpush