@extends('layouts.app')
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

            <form id="accountForm">
                <!-- Correo electrónico -->
                <div class="form-group">
                    <label class="form-label" for="inputEmail">Correo electrónico</label>
                    <span class="form-hint">Este correo se usa para iniciar sesión y recibir notificaciones</span>
                    <div class="input-with-badge">
                        <input
                            type="email"
                            class="form-input"
                            id="inputEmail"
                            value="lucas.silva@email.com"
                            placeholder="tu@email.com" />
                        <span class="badge badge-verified">
                            <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                    d="M5 13l4 4L19 7" />
                            </svg>
                            Verificado
                        </span>
                    </div>
                </div>

                <!-- Teléfono -->
                <div class="form-group">
                    <label class="form-label" for="inputPhone">Número de teléfono</label>
                    <span class="form-hint">Opcional · Usado para verificación en dos pasos</span>
                    <div class="input-phone-group">
                        <select class="form-input phone-prefix" id="inputPhonePrefix">
                            <option value="+57">🇨🇴 +57</option>
                            <option value="+52">🇲🇽 +52</option>
                            <option value="+54">🇦🇷 +54</option>
                            <option value="+34">🇪🇸 +34</option>
                            <option value="+51">🇵🇪 +51</option>
                            <option value="+56">🇨🇱 +56</option>
                            <option value="+1">🇺🇸 +1</option>
                        </select>
                        <input
                            type="tel"
                            class="form-input phone-number"
                            id="inputPhone"
                            value="310 456 7890"
                            placeholder="Número de teléfono" />
                    </div>
                </div>

                <!-- Idioma -->
                <div class="form-group">
                    <label class="form-label" for="inputLanguage">Idioma</label>
                    <select class="form-input" id="inputLanguage">
                        <option selected>🌐 Español</option>
                        <option>🌐 English</option>
                        <option>🌐 Português</option>
                        <option>🌐 Français</option>
                        <option>🌐 Deutsch</option>
                    </select>
                </div>

                <!-- Acciones -->
                <div class="form-actions">
                    <button
                        type="button"
                        class="btn-cancel"
                        onclick="window.location.href='perfil.html'">
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
                        <span class="logout-meta">lucas.silva@email.com · {{ auth()->user()->name ?? 'Lucas Silva Daniel' }}</span>
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
                <!-- Desactivar cuenta -->
                <div class="danger-item">
                    <div class="danger-info">
                        <span class="danger-label">Desactivar cuenta</span>
                        <span class="danger-desc">
                            Tu perfil dejará de ser visible. Podrás reactivarla iniciando sesión.
                        </span>
                    </div>
                    <button type="button" class="btn-danger btn-danger--outline" id="btnDeactivate">
                        Desactivar
                    </button>
                </div>

                <!-- Eliminar cuenta -->
                <div class="danger-item">
                    <div class="danger-info">
                        <span class="danger-label">Eliminar cuenta permanentemente</span>
                        <span class="danger-desc">
                            Se borrarán todos tus datos, proyectos y publicaciones. Esta acción no se puede deshacer.
                        </span>
                    </div>
                    <button type="button" class="btn-danger btn-danger--filled" id="btnDelete">
                        Eliminar cuenta
                    </button>
                </div>
            </div>
        </section>

    </main>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('css/configuration.css') }}" />
<link rel="stylesheet" href="{{ asset('css/account.css') }}" />
@endpush

@push('scripts')
<script>
    // ── 2FA Toggle ──────────────────────────────────────────
    const toggle2FA = document.getElementById('toggle2FA');
    const twoFaOptions = document.getElementById('twoFaOptions');

    toggle2FA.addEventListener('change', () => {
        const hint = toggle2FA.closest('.toggle-row').querySelector('.toggle-hint strong');
        if (toggle2FA.checked) {
            twoFaOptions.style.display = 'flex';
            hint.textContent = 'activada';
        } else {
            twoFaOptions.style.display = 'none';
            hint.textContent = 'desactivada';
        }
    });

    // ── Setup 2FA ───────────────────────────────────────────
    document.querySelector('.btn-setup-2fa')?.addEventListener('click', () => {
        const method = document.querySelector('input[name="twoFaMethod"]:checked')?.value;
        console.log('Configurando 2FA con método:', method);
        // Aquí abrirías el modal de configuración correspondiente
        alert(`⚙️ Configurando 2FA con: ${method === 'app' ? 'Aplicación de autenticación' : 'SMS'}`);
    });

    // ── Account form submit ─────────────────────────────────
    document.getElementById('accountForm')?.addEventListener('submit', (e) => {
        e.preventDefault();
        const formData = {
            email: document.getElementById('inputEmail').value,
            phone: document.getElementById('inputPhone').value,
            prefix: document.getElementById('inputPhonePrefix').value,
            language: document.getElementById('inputLanguage').value,
        };
        console.log('Guardando cuenta:', formData);
        // fetch('/api/account/update', { method: 'POST', body: JSON.stringify(formData) })
        alert('✅ Información de cuenta actualizada');
    });

    // ── Deactivate account ──────────────────────────────────
    document.getElementById('btnDeactivate')?.addEventListener('click', () => {
        if (confirm('¿Desactivar tu cuenta? Tu perfil dejará de ser visible hasta que vuelvas a iniciar sesión.')) {
            console.log('Cuenta desactivada');
            // fetch('/api/account/deactivate', { method: 'POST' })
        }
    });

    // ── Delete account ──────────────────────────────────────
    document.getElementById('btnDelete')?.addEventListener('click', () => {
        const confirmText = prompt('Esta acción es permanente. Escribe "ELIMINAR" para confirmar:');
        if (confirmText === 'ELIMINAR') {
            console.log('Cuenta eliminada permanentemente');
            // fetch('/api/account/delete', { method: 'DELETE' })
            alert('Cuenta eliminada. Redirigiendo...');
            // window.location.href = '/';
        } else if (confirmText !== null) {
            alert('El texto ingresado no coincide. Operación cancelada.');
        }
    });

    // ── Sidebar navigation ──────────────────────────────────
    document.querySelectorAll('.sidebar-item').forEach(item => {
        item.addEventListener('click', function() {
            document.querySelectorAll('.sidebar-item').forEach(i => i.classList.remove('active'));
            this.classList.add('active');
            console.log('Navegando a:', this.textContent.trim());
        });
    });

    console.log('✅ Account configuration page loaded');
</script>
@endpush