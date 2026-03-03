@extends('layouts.app')
@section('title', 'Cuenta')
@section('content')
@include('components.topbar')
<!-- ══════════════════════════════════════════
     ACCOUNT CONFIGURATION LAYOUT
══════════════════════════════════════════ -->
<div class="edit-layout">
    @include('components.sidebar')

    <!-- ──── MAIN CONTENT ──── -->
    <main class="main-content">
        <h1 class="page-title">Cuenta</h1>
        <p class="page-subtitle">
            Administra la información de tu cuenta y preferencias de acceso en Fluxa
        </p>

        <!-- ── SECCIÓN: Información de cuenta ─────────────────── -->
        <section class="config-section">
            <h2 class="section-title">Información de cuenta</h2>

            @if(!Auth::user()->email_verified_at)
            <form method="POST" action="{{ route('verification.send') }}" id="form-verify-email">
                @csrf
            </form>
            @endif

            <form id="accountForm" method="POST" action="{{ route('account.edit') }}">
                @csrf

                <!-- Correo electrónico -->
                <div class="form-group">
                    <label class="form-label" for="inputEmail">Correo electrónico</label>
                    <span class="form-hint">Este correo se usa para iniciar sesión y recibir notificaciones</span>
                    <div class="input-with-badge">
                        <input
                            type="email"
                            class="form-input {{ $errors->has('email') ? 'input-error' : '' }}"
                            id="inputEmail"
                            name="email"
                            value="{{ old('email', Auth()->user()->email ?? '') }}"
                            placeholder="tu@email.com" />

                        @if(Auth::user()->email_verified_at)
                        <span class="badge badge-verified">
                            <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                    d="M5 13l4 4L19 7" />
                            </svg>
                            Verificado
                        </span>
                        @else
                        {{-- form="form-verify-email" lo vincula al form de arriba --}}
                        <button type="submit" form="form-verify-email" class="btn btn-link">
                            Verificar email
                        </button>

                        @if(session('status') === 'verification-link-sent')
                        <p class="text-sm text-success">¡Enlace enviado! Revisa tu bandeja.</p>
                        @endif
                        @endif
                    </div>
                    @error('email')
                    <span class="form-error text-red-500">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Teléfono -->
                <div class="form-group">
                    <label class="form-label" for="inputPhone">Número de teléfono</label>
                    <span class="form-hint">Opcional · Usado para verificación en dos pasos</span>
                    <div class="input-phone-group">
                        @php $currentPhoneCode = old('phone_code', Auth()->user()->profile->phone_code ?? '+57') @endphp

                        <select class="form-input phone-prefix {{ $errors->has('phone_code') ? 'input-error' : '' }}" id="inputPhonePrefix" name="phone_code">
                            <option value="+57" {{ $currentPhoneCode == '+57' ? 'selected' : '' }}>🇨🇴 +57</option>
                            <option value="+52" {{ $currentPhoneCode == '+52' ? 'selected' : '' }}>🇲🇽 +52</option>
                            <option value="+54" {{ $currentPhoneCode == '+54' ? 'selected' : '' }}>🇦🇷 +54</option>
                            <option value="+34" {{ $currentPhoneCode == '+34' ? 'selected' : '' }}>🇪🇸 +34</option>
                            <option value="+51" {{ $currentPhoneCode == '+51' ? 'selected' : '' }}>🇵🇪 +51</option>
                            <option value="+56" {{ $currentPhoneCode == '+56' ? 'selected' : '' }}>🇨🇱 +56</option>
                            <option value="+1" {{ $currentPhoneCode == '+1'  ? 'selected' : '' }}>🇺🇸 +1</option>
                        </select>

                        <input
                            type="tel"
                            class="form-input phone-number {{ $errors->has('phone_number') ? 'input-error' : '' }}"
                            id="inputPhone"
                            name="phone_number"
                            value="{{ old('phone_number', Auth()->user()->profile->phone_number ?? '') }}"
                            placeholder="Número de teléfono" />
                    </div>
                    @error('phone_code')
                    <span class="form-error text-red-500">{{ $message }}</span>
                    @enderror
                    @error('phone_number')
                    <span class="form-error text-red-500">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Idioma -->
                <div class="form-group">
                    <label class="form-label" for="inputLanguage">Idioma</label>
                    <select class="form-input {{ $errors->has('language') ? 'input-error' : '' }}" id="inputLanguage" name="language">
                        @php $currentLanguage = old('language', Auth()->user()->profile->language ?? 'es') @endphp
                        <option value="es" {{ $currentLanguage == 'es' ? 'selected' : '' }}>Español</option>
                        <option value="en" {{ $currentLanguage == 'en' ? 'selected' : '' }}>English</option>
                        <option value="pt" {{ $currentLanguage == 'pt' ? 'selected' : '' }}>Português</option>
                        <option value="fr" {{ $currentLanguage == 'fr' ? 'selected' : '' }}>Français</option>
                        <option value="de" {{ $currentLanguage == 'de' ? 'selected' : '' }}>Deutsch</option>
                    </select>
                    @error('language')
                    <span class="form-error text-red-500">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Acciones -->
                <div class="form-actions">
                    <button id="guardarCambiosPerfil" type="button" class="btn-cancel">
                        Cancelar
                    </button>
                    <button type="submit" class="btn-submit">Guardar cambios</button>
                </div>
            </form>
        </section>

        <div class="section-divider"></div>

        <!-- ── SECCIÓN: Verificación en dos pasos ─────────────── -->
        <section class="config-section">
            <h2 class="section-title">Verificación en dos pasos</h2>
            <p class="section-description">
                Agrega una capa adicional de seguridad a tu cuenta. Al activarla, se te pedirá
                un código de verificación cada vez que inicies sesión desde un dispositivo nuevo.
            </p>

            <div class="toggle-row">
                <div class="toggle-info">
                    <span class="toggle-label">Autenticación de dos factores</span>
                    <span class="toggle-hint">Actualmente <strong>desactivada</strong></span>
                </div>
                <label class="toggle-switch">
                    <input type="checkbox" id="toggle2FA" />
                    <span class="toggle-track"></span>
                </label>
            </div>

            <div class="two-fa-options" id="twoFaOptions" style="display:none;">
                <div class="radio-card">
                    <input type="radio" name="twoFaMethod" id="methodApp" value="app" checked />
                    <label for="methodApp" class="radio-label">
                        <span class="radio-icon">📱</span>
                        <div>
                            <span class="radio-title">Aplicación de autenticación</span>
                            <span class="radio-desc">Google Authenticator, Authy, etc.</span>
                        </div>
                    </label>
                </div>
                <div class="radio-card">
                    <input type="radio" name="twoFaMethod" id="methodSMS" value="sms" />
                    <label for="methodSMS" class="radio-label">
                        <span class="radio-icon">💬</span>
                        <div>
                            <span class="radio-title">Mensaje de texto (SMS)</span>
                            <span class="radio-desc">Recibe un código en tu teléfono</span>
                        </div>
                    </label>
                </div>
                <button type="button" class="btn-setup-2fa">Configurar ahora</button>
            </div>
        </section>

        <div class="section-divider"></div>

        <!-- ── SECCIÓN: Cerrar sesión ──────────────────────────── -->
        <section class="config-section">
            <h2 class="section-title">Sesión</h2>
            <p class="section-description">
                Cierra tu sesión actual en este dispositivo. Tendrás que volver a iniciar sesión para acceder a tu cuenta.
            </p>

            <div class="logout-row">
                <div class="logout-info">
                    <div class="logout-icon">
                        <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div class="logout-details">
                        <span class="logout-device">Sesión actual</span>
                        <span class="logout-meta">{{ Auth()->user()->email ?? '' }} · {{ auth()->user()->name ?? '' }}</span>
                    </div>
                    <span class="badge badge-current">Activa</span>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn-logout" id="btnLogout">
                        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                        Cerrar sesión
                    </button>
                </form>
            </div>
        </section>

        <div class="section-divider"></div>

        <!-- ── SECCIÓN: Zona de peligro ────────────────────────── -->
        <section class="config-section danger-section">
            <h2 class="section-title section-title--danger">Zona de peligro</h2>
            <p class="section-description">
                Estas acciones son permanentes e irreversibles. Procede con precaución.
            </p>

            <div class="danger-actions">
                <!-- Desactivar -->
                <div class="danger-item">
                    <div class="danger-info">
                        <span class="danger-label">Desactivar cuenta</span>
                        <span class="danger-desc">Tu perfil dejará de ser visible. Podrás reactivarla iniciando sesión.</span>
                    </div>
                    <form action="{{ route('account.deactivate') }}" method="POST" id="formDeactivate">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn-danger btn-danger--outline" id="btnDeactivate">
                            Desactivar
                        </button>
                    </form>
                </div>

                <!-- Eliminar -->
                <div class="danger-item">
                    <div class="danger-info">
                        <span class="danger-label">Eliminar cuenta permanentemente</span>
                        <span class="danger-desc">Se borrarán todos tus datos, proyectos y publicaciones. Esta acción no se puede deshacer.</span>
                    </div>
                    <form data-username="{{ Auth()->user()->username }}"
                        action="{{ route('account.destroy') }}" method="POST" id="formDestroy">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-danger btn-danger--filled" id="btnDelete">
                            Eliminar cuenta
                        </button>
                    </form>
                </div>
            </div>
        </section>

    </main>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('css/account.css') }}" />
@endpush

@push('scripts')
<script src="{{ asset('js/destroyAccount.js') }}"></script>
@endpush