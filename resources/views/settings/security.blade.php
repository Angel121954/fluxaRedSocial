@extends('layouts.app')
@section('title', 'Seguridad')
@section('content')
<x-topbar :profile="$profile" />
{{-- ══════════════════════════════════════════
     SECURITY CONFIGURATION LAYOUT
═════════════════════════════════════════ --}}
<div class="edit-layout">
    <x-sidebar :profile="$profile" />

    {{-- ──── MAIN CONTENT ──── --}}
    <main class="main-content">
        <h1 class="page-title">Seguridad</h1>
        <p class="page-subtitle">Protege tu cuenta con las configuraciones de seguridad disponibles.</p>

        {{-- ── SECCIÓN: Verificación en dos pasos ─────────────────── --}}
        <section class="config-section">
            <div class="sec-card">
                <div class="sec-card__body">
                    <div class="sec-card__icon sec-card__icon--teal">
                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
                            <polyline points="9 12 11 14 15 10" />
                        </svg>
                    </div>
                    <div class="sec-card__text">
                        <h2 class="sec-card__title">Verificación en dos pasos</h2>
                        <p class="sec-card__desc">
                            Añade una capa adicional de <strong>seguridad a tu cuenta</strong> activando la
                            verificación en dos pasos para protegerla contra accesos no autorizados.
                        </p>
                        <a href="#" class="sec-btn sec-btn--primary">
                            Activar verificación en dos pasos
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                <polyline points="9 18 15 12 9 6" />
                            </svg>
                        </a>
                    </div>
                </div>
                <div class="sec-card__illus" aria-hidden="true">
                    <div class="illus-phone">
                        <div class="illus-phone__screen">
                            <div class="illus-phone__shield">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
                                </svg>
                                <span class="illus-phone__check">✓</span>
                            </div>
                            <div class="illus-phone__dots">
                                <span></span><span></span><span></span><span></span><span></span>
                            </div>
                        </div>
                    </div>
                    <div class="illus-shield-big">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
                        </svg>
                        <span>✓</span>
                    </div>
                </div>
            </div>
        </section>

        <div class="section-divider"></div>

        {{-- ── SECCIÓN: Cambiar contraseña ─────────────────────────── --}}
        <section class="config-section">
            <div class="sec-card">
                <div class="sec-card__body">
                    <div class="sec-card__icon sec-card__icon--orange">
                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2" />
                            <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                        </svg>
                    </div>
                    <div class="sec-card__text">
                        <h2 class="sec-card__title">Cambiar contraseña</h2>
                        <p class="sec-card__desc">
                            Actualiza tu <strong>contraseña</strong> regularmente para mantener tu cuenta segura.
                        </p>
                        <button type="button" class="sec-btn sec-btn--outline" id="btnOpenPasswordModal">
                            Cambiar contraseña
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                <polyline points="9 18 15 12 9 6" />
                            </svg>
                        </button>
                    </div>
                </div>
                <div class="sec-card__illus" aria-hidden="true">
                    <div class="illus-laptop">
                        <div class="illus-laptop__screen">
                            <div class="illus-map-dot"></div>
                            <svg class="illus-map-arrow" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10" />
                                <polyline points="12 8 16 12 12 16" />
                                <line x1="8" y1="12" x2="16" y2="12" />
                            </svg>
                        </div>
                        <div class="illus-laptop__base"></div>
                    </div>
                    <div class="illus-phone-sm">
                        <div class="illus-check-circle">✓</div>
                    </div>
                </div>
            </div>
        </section>

        <div class="section-divider"></div>

        {{-- ── SECCIÓN: Actividades recientes ──────────────────────── --}}
        <section class="config-section">
            <div class="sec-card">
                <div class="sec-card__body">
                    <div class="sec-card__icon sec-card__icon--teal">
                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                            <circle cx="11" cy="11" r="8" />
                            <line x1="21" y1="21" x2="16.65" y2="16.65" />
                        </svg>
                    </div>
                    <div class="sec-card__text">
                        <h2 class="sec-card__title">Actividades recientes</h2>
                        <p class="sec-card__desc">
                            Revisa los dispositivos y <strong>ubicaciones</strong> recientes desde los que se ha accedido a tu cuenta.
                        </p>
                        <a href="#" class="sec-btn sec-btn--outline">
                            Ver actividad reciente
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                <polyline points="9 18 15 12 9 6" />
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        </section>

    </main>
</div>

{{-- ══════════════════════════════════════════
     MODAL: Cambiar contraseña
══════════════════════════════════════════ --}}
<div class="pwd-modal-backdrop" id="passwordModal" role="dialog" aria-modal="true" aria-labelledby="modalPasswordTitle">
    <div class="pwd-modal">

        {{-- Header --}}
        <div class="pwd-modal__header">
            <div class="pwd-modal__header-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2" />
                    <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                </svg>
            </div>
            <div>
                <h2 class="pwd-modal__title" id="modalPasswordTitle">Cambiar contraseña</h2>
                <p class="pwd-modal__subtitle">Elige una contraseña segura que no uses en otros sitios.</p>
            </div>
            <button class="pwd-modal__close" id="btnClosePasswordModal" aria-label="Cerrar">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                    <line x1="18" y1="6" x2="6" y2="18" />
                    <line x1="6" y1="6" x2="18" y2="18" />
                </svg>
            </button>
        </div>

        {{-- Form --}}
        <form method="POST" action="{{ route('password.update') }}" id="formChangePassword" class="pwd-modal__form">
            @csrf
            @method('PUT')

            {{-- Contraseña actual --}}
            <div class="pwd-field">
                <label class="pwd-field__label" for="current_password">Contraseña actual</label>
                <div class="pwd-field__wrap">
                    <input type="password" id="current_password" name="current_password"
                        class="pwd-field__input @error('current_password', 'updatePassword') pwd-field__input--error @enderror"
                        placeholder="Tu contraseña actual" autocomplete="current-password" />
                    <button type="button" class="pwd-field__toggle" data-target="current_password" aria-label="Mostrar/ocultar">
                        <svg class="eye-icon eye-icon--show" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                            <circle cx="12" cy="12" r="3" />
                        </svg>
                        <svg class="eye-icon eye-icon--hide" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" style="display:none">
                            <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94" />
                            <path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19" />
                            <line x1="1" y1="1" x2="23" y2="23" />
                        </svg>
                    </button>
                </div>
                @error('current_password', 'updatePassword')
                <span class="pwd-field__error">
                    <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="10" />
                        <line x1="12" y1="8" x2="12" y2="12" />
                        <line x1="12" y1="16" x2="12.01" y2="16" />
                    </svg>
                    {{ $message }}
                </span>
                @enderror
            </div>

            <div class="pwd-modal__divider"></div>

            {{-- Nueva contraseña --}}
            <div class="pwd-field">
                <label class="pwd-field__label" for="password">Nueva contraseña</label>
                <div class="pwd-field__wrap">
                    <input type="password" id="password" name="password"
                        class="pwd-field__input @error('password', 'updatePassword') pwd-field__input--error @enderror"
                        placeholder="Mínimo 8 caracteres" autocomplete="new-password" />
                    <button type="button" class="pwd-field__toggle" data-target="password" aria-label="Mostrar/ocultar">
                        <svg class="eye-icon eye-icon--show" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                            <circle cx="12" cy="12" r="3" />
                        </svg>
                        <svg class="eye-icon eye-icon--hide" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" style="display:none">
                            <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94" />
                            <path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19" />
                            <line x1="1" y1="1" x2="23" y2="23" />
                        </svg>
                    </button>
                </div>
                {{-- Barra de fortaleza --}}
                <div class="pwd-strength" id="pwdStrength">
                    <div class="pwd-strength__bar">
                        <div class="pwd-strength__fill" id="pwdStrengthFill"></div>
                    </div>
                    <span class="pwd-strength__label" id="pwdStrengthLabel"></span>
                </div>
                @error('password', 'updatePassword')
                <span class="pwd-field__error">
                    <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="10" />
                        <line x1="12" y1="8" x2="12" y2="12" />
                        <line x1="12" y1="16" x2="12.01" y2="16" />
                    </svg>
                    {{ $message }}
                </span>
                @enderror
            </div>

            {{-- Confirmar contraseña --}}
            <div class="pwd-field">
                <label class="pwd-field__label" for="password_confirmation">Confirmar nueva contraseña</label>
                <div class="pwd-field__wrap">
                    <input type="password" id="password_confirmation" name="password_confirmation"
                        class="pwd-field__input @error('password_confirmation', 'updatePassword') pwd-field__input--error @enderror"
                        placeholder="Repite la nueva contraseña" autocomplete="new-password" />
                    <button type="button" class="pwd-field__toggle" data-target="password_confirmation" aria-label="Mostrar/ocultar">
                        <svg class="eye-icon eye-icon--show" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                            <circle cx="12" cy="12" r="3" />
                        </svg>
                        <svg class="eye-icon eye-icon--hide" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" style="display:none">
                            <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94" />
                            <path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19" />
                            <line x1="1" y1="1" x2="23" y2="23" />
                        </svg>
                    </button>
                </div>
                <span class="pwd-match-hint" id="pwdMatchHint"></span>
                @error('password_confirmation', 'updatePassword')
                <span class="pwd-field__error">
                    <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="10" />
                        <line x1="12" y1="8" x2="12" y2="12" />
                        <line x1="12" y1="16" x2="12.01" y2="16" />
                    </svg>
                    {{ $message }}
                </span>
                @enderror
            </div>

            {{-- Acciones --}}
            <div class="pwd-modal__actions">
                <button type="button" class="btn-cancel" id="btnCancelPasswordModal">Cancelar</button>
                <button type="submit" class="btn-submit" id="btnSubmitPassword">
                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
                    </svg>
                    Guardar contraseña
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Toast: contraseña actualizada --}}
@if(session('status') === 'password-updated')
<div class="pwd-toast" id="pwdToast" role="status">
    <div class="pwd-toast__icon">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
            <polyline points="20 6 9 17 4 12" />
        </svg>
    </div>
    <span>¡Contraseña actualizada correctamente!</span>
</div>
@endif

@endsection

@push('styles')
@vite('resources/css/profile/shared.css')
@vite('resources/css/profile/sidebar.css')
@vite('resources/css/settings/security.css')
@endpush

@push('scripts')
@vite('resources/js/shared/security.js')
@vite('resources/js/profile/avatar.js')
@endpush