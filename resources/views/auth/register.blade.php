@extends('layouts.app')
@section('content')
<!-- Success Notification -->
<div id="notification" class="notification">
    <svg
        width="24"
        height="24"
        fill="none"
        stroke="currentColor"
        stroke-width="2"
        stroke-linecap="round"
        stroke-linejoin="round"
        style="color: var(--success)">
        <polyline points="20 6 9 17 4 12"></polyline>
    </svg>
    <span>¡Cuenta creada exitosamente!</span>
</div>

<div class="min-h-screen flex items-center justify-center p-4 md:p-8">
    <div class="w-full max-w-6xl grid md:grid-cols-2 gap-8 items-center">
        <!-- Form Side -->
        <div class="flex flex-col">
            <!-- Header -->
            <div class="mb-8">
                <img src="{{ asset('img/logoFluxa.png') }}" alt="Fluxa" class="logo-img" />
                <p
                    style="
                color: var(--text-secondary);
                font-size: 1.0625rem;
                line-height: 1.6;
                max-width: 420px;
              ">
                    Únete a la red social para desarrolladores del SENA que construyen
                    en público.
                </p>
            </div>

            <!-- Form Card -->
            <div class="form-card">
                <h2
                    style="
                font-size: 1.625rem;
                font-weight: 700;
                margin-bottom: 1.75rem;
                color: var(--text-primary);
              ">
                    Crear cuenta
                </h2>

                <form id="registerForm" method="POST" action="{{ route('register') }}" novalidate>
                    @csrf
                    <!-- Name Field -->
                    <div class="input-group">
                        <label for="name" class="input-label">Nombre completo</label>
                        <div class="input-wrapper">
                            <input
                                value="{{ old('name') }}"
                                type="text"
                                id="name"
                                name="name"
                                class="input-field"
                                placeholder="Juan Pérez"
                                autocomplete="name"
                                required />
                            <svg
                                class="input-icon"
                                width="20"
                                height="20"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            <svg
                                class="success-icon"
                                width="20"
                                height="20"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24">
                                <polyline
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    points="20 6 9 17 4 12" />
                            </svg>
                        </div>
                        @error('name')
                        <div class="error-message show" id="nameError">
                            <svg
                                width="14"
                                height="14"
                                fill="currentColor"
                                viewBox="0 0 16 16">
                                <path
                                    d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z" />
                                <path
                                    d="M7.002 11a1 1 0 1 1 2 0 1 1 0 0 1-2 0zM7.1 4.995a.905.905 0 1 1 1.8 0l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 4.995z" />
                            </svg>
                            <span class="text-red-500">{{ $message }}</span>
                        </div>
                        @enderror
                    </div>

                    <!-- Username Field -->
                    <div class="input-group">
                        <label for="username" class="input-label">Nombre de usuario</label>
                        <div class="input-wrapper">
                            <input
                                value="{{ old('username') }}"
                                type="text"
                                id="username"
                                name="username"
                                class="input-field"
                                placeholder="juanperez"
                                autocomplete="username"
                                required />
                            <svg
                                class="input-icon"
                                width="20"
                                height="20"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                            </svg>
                            <svg
                                class="success-icon"
                                width="20"
                                height="20"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24">
                                <polyline
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    points="20 6 9 17 4 12" />
                            </svg>
                        </div>
                        @error('username')
                        <div class="error-message show" id="usernameError">
                            <svg
                                width="14"
                                height="14"
                                fill="currentColor"
                                viewBox="0 0 16 16">
                                <path
                                    d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z" />
                                <path
                                    d="M7.002 11a1 1 0 1 1 2 0 1 1 0 0 1-2 0zM7.1 4.995a.905.905 0 1 1 1.8 0l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 4.995z" />
                            </svg>
                            <span class="text-red-500">{{ $message }}</span>
                        </div>
                        @enderror
                    </div>

                    <!-- Email Field -->
                    <div class="input-group">
                        <label for="email" class="input-label">Email</label>
                        <div class="input-wrapper">
                            <input
                                value="{{ old('email') }}"
                                type="email"
                                id="email"
                                name="email"
                                class="input-field"
                                placeholder="tu@email.com"
                                autocomplete="email"
                                required />
                            <svg
                                class="input-icon"
                                width="20"
                                height="20"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                            <svg
                                class="success-icon"
                                width="20"
                                height="20"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24">
                                <polyline
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    points="20 6 9 17 4 12" />
                            </svg>
                        </div>
                        @error('email')
                        <div class="error-message show" id="emailError">
                            <svg
                                width="14"
                                height="14"
                                fill="currentColor"
                                viewBox="0 0 16 16">
                                <path
                                    d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z" />
                                <path
                                    d="M7.002 11a1 1 0 1 1 2 0 1 1 0 0 1-2 0zM7.1 4.995a.905.905 0 1 1 1.8 0l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 4.995z" />
                            </svg>
                            <span>{{ $message }}</span>
                        </div>
                        @enderror
                    </div>

                    <!-- Password Field -->
                    <div class="input-group">
                        <label for="password" class="input-label">Contraseña</label>
                        <div class="input-wrapper">
                            <input
                                type="password"
                                id="password"
                                name="password"
                                class="input-field"
                                placeholder="••••••••"
                                autocomplete="new-password"
                                required
                                minlength="8" />
                            <svg
                                class="input-icon"
                                width="20"
                                height="20"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                            <button type="button" class="toggle-password" aria-label="Mostrar contraseña">
    <!-- Ojo abierto -->
    <svg class="eye-open" width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
    </svg>
    <!-- Ojo cerrado -->
    <svg class="eye-closed" width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display:none">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88L6.59 6.59m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
    </svg>
</button>
                        </div>
                        @error('password')
                        <div class="error-message show" id="passwordError">
                            <svg
                                width="14"
                                height="14"
                                fill="currentColor"
                                viewBox="0 0 16 16">
                                <path
                                    d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z" />
                                <path
                                    d="M7.002 11a1 1 0 1 1 2 0 1 1 0 0 1-2 0zM7.1 4.995a.905.905 0 1 1 1.8 0l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 4.995z" />
                            </svg>
                            <span>{{ $message }}</span>
                        </div>
                        @enderror

                        <!-- Password Strength Indicator -->
                        <div class="password-strength" id="passwordStrength">
                            <div class="strength-label">
                                <span style="color: var(--text-secondary)">Fortaleza:</span>
                                <span
                                    id="strengthText"
                                    style="color: var(--error); font-size: 0.75rem">Débil</span>
                            </div>
                            <div class="strength-bars">
                                <div class="strength-bar" id="bar1"></div>
                                <div class="strength-bar" id="bar2"></div>
                                <div class="strength-bar" id="bar3"></div>
                                <div class="strength-bar" id="bar4"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Confirm Password Field -->
                    <div class="input-group">
                        <label for="password_confirmation" class="input-label">Confirmar contraseña</label>
                        <div class="input-wrapper">
                            <input
                                type="password"
                                id="password_confirmation"
                                name="password_confirmation"
                                class="input-field"
                                placeholder="••••••••"
                                autocomplete="new-password"
                                required />
                            <svg
                                class="input-icon"
                                width="20"
                                height="20"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <!-- Confirmar contraseña - botón toggle -->
<button type="button" class="toggle-password" id="togglePasswordConfirm" aria-label="Mostrar contraseña">
    <!-- Ojo abierto -->
    <svg class="eye-open" width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
    </svg>
    <!-- Ojo cerrado -->
    <svg class="eye-closed" width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display:none">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88L6.59 6.59m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
    </svg>
</button>
                        </div>
                        @error('password')
                        <div class="error-message show" id="passwordConfirmError">
                            <svg
                                width="14"
                                height="14"
                                fill="currentColor"
                                viewBox="0 0 16 16">
                                <path
                                    d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z" />
                                <path
                                    d="M7.002 11a1 1 0 1 1 2 0 1 1 0 0 1-2 0zM7.1 4.995a.905.905 0 1 1 1.8 0l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 4.995z" />
                            </svg>
                            <span>{{ $message }}</span>
                        </div>
                        @enderror
                    </div>

                    <!-- Submit Button -->
                    <button
                        type="submit"
                        class="btn btn-primary"
                        id="submitBtn"
                        style="margin-top: 1.5rem">
                        <span class="loader"></span>
                        <span class="btn-text">Crear cuenta</span>
                    </button>
                </form>

                <!-- Login Link -->
                <p
                    style="
                text-align: center;
                margin-top: 1.75rem;
                color: var(--text-secondary);
                font-size: 0.9375rem;
              ">
                    ¿Ya tienes una cuenta?
                    <a href="{{ route('login') }}" class="link">Iniciar sesión</a>
                </p>
            </div>
        </div>

        <!-- Illustration Side -->
        <div class="hidden md:flex illustration-side">
            <img
                src="{{ asset('img/desarrolladorRegistro.png') }}"
                alt="Desarrollador trabajando"
                class="developer-image" />
        </div>
    </div>
</div>
@endsection

@push('styles')
<!--Estilo personalizado de registro-->
<link rel="stylesheet" href="{{ asset('css/register.css') }}" />
@endpush

@push('scripts')
<script src="{{ asset('js/passwordVisibility.js') }}"></script>
@endpush