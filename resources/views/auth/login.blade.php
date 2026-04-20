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
    <span>¡Inicio de sesión exitoso!</span>
</div>

<div class="min-h-screen flex items-center justify-center p-4 md:p-8">
    <div class="w-full max-w-6xl grid md:grid-cols-2 gap-8 items-center">
        <!-- Form Side -->
        <div class="flex flex-col">
            <x-header />
            <!-- Form Card -->
            <div class="form-card">
                <h2
                    style="
                font-size: 1.625rem;
                font-weight: 700;
                margin-bottom: 1.75rem;
                color: var(--text-primary);
              ">
                    Iniciar sesión
                </h2>

                @if(session('status'))
                <div class="error-message show" style="margin-bottom: 1rem; padding: 0.75rem 1rem; background: #f0fdfd; border-color: #12b3b6; color: #0d9b9e;">
                    <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                    </svg>
                    <span>{{ session('status') }}</span>
                </div>
                @endif

                <form id="loginForm" method="post" action="{{ route('login') }}" novalidate>
                    @csrf
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
                                autocomplete="current-password"
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
                    </div>

                    <!-- Forgot Password -->
                    <div style="text-align: right; margin-bottom: 1.5rem">
                        <a
                            href="{{ route('password.request') }}"
                            class="link"
                            style="font-size: 0.875rem">
                            ¿Olvidaste tu contraseña?
                        </a>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="btn btn-primary" id="submitBtn">
                        <span class="loader"></span>
                        <span class="btn-text">Iniciar sesión</span>
                    </button>

                    <!-- Divider -->
                    <div class="divider">
                        <span>o continúa con</span>
                    </div>

                    <!-- Google Button -->
                    <a href="{{ route('social.redirect', 'google') }}" type="button" class="btn btn-secondary" id="googleBtn">
                        <svg width="20" height="20" viewBox="0 0 24 24">
                            <path
                                fill="#4285F4"
                                d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" />
                            <path
                                fill="#34A853"
                                d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" />
                            <path
                                fill="#FBBC05"
                                d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" />
                            <path
                                fill="#EA4335"
                                d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" />
                        </svg>
                        <span>Iniciar sesión con Google</span>
                    </a>

                    <!-- GitHub Button -->
                    <a
                        href="{{ route('social.redirect', 'github') }}"
                        type="button"
                        class="btn btn-github"
                        id="githubBtn"
                        style="margin-top: 0.75rem">
                        <svg
                            width="20"
                            height="20"
                            fill="currentColor"
                            viewBox="0 0 24 24">
                            <path
                                fill-rule="evenodd"
                                clip-rule="evenodd"
                                d="M12 2C6.477 2 2 6.477 2 12c0 4.42 2.865 8.17 6.839 9.49.5.092.682-.217.682-.482 0-.237-.008-.866-.013-1.7-2.782.603-3.369-1.34-3.369-1.34-.454-1.156-1.11-1.463-1.11-1.463-.908-.62.069-.608.069-.608 1.003.07 1.531 1.03 1.531 1.03.892 1.529 2.341 1.087 2.91.831.092-.646.35-1.086.636-1.336-2.22-.253-4.555-1.11-4.555-4.943 0-1.091.39-1.984 1.029-2.683-.103-.253-.446-1.27.098-2.647 0 0 .84-.269 2.75 1.025A9.578 9.578 0 0112 6.836c.85.004 1.705.114 2.504.336 1.909-1.294 2.747-1.025 2.747-1.025.546 1.377.203 2.394.1 2.647.64.699 1.028 1.592 1.028 2.683 0 3.842-2.339 4.687-4.566 4.935.359.309.678.919.678 1.852 0 1.336-.012 2.415-.012 2.743 0 .267.18.578.688.48C19.138 20.167 22 16.418 22 12c0-5.523-4.477-10-10-10z" />
                        </svg>
                        <span>Iniciar sesión con GitHub</span>
                    </a>

                    <!-- Facebook Button -->
                    <a
                        href="{{ route('social.redirect', 'facebook') }}"
                        type="button"
                        class="btn btn-facebook"
                        id="facebookBtn"
                        style="margin-top: 0.75rem">
                        <svg
                            width="20"
                            height="20"
                            fill="currentColor"
                            viewBox="0 0 24 24">
                            <path
                                d="M24 12.073C24 5.405 18.627 0 12 0S0 5.405 0 12.073C0 18.1 4.388 23.094 10.125 24v-8.437H7.078v-3.49h3.047V9.41c0-3.025 1.792-4.697 4.533-4.697 1.312 0 2.686.236 2.686.236v2.97h-1.513c-1.491 0-1.956.93-1.956 1.887v2.267h3.328l-.532 3.49h-2.796V24C19.612 23.094 24 18.1 24 12.073z" />
                        </svg>
                        <span>Iniciar sesión con Facebook</span>
                    </a>

                    <!-- Divider -->
                    <div class="divider">
                        <span>o explora sin cuenta</span>
                    </div>

                    <a href="{{ route('auth.guest') }}" class="btn btn-guest">
                        <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        <span>
                            Continuar como visitante
                        </span>
                    </a>
                </form>

                <!-- Sign Up Link -->
                <p
                    style="
                text-align: center;
                margin-top: 1.75rem;
                color: var(--text-secondary);
                font-size: 0.9375rem;
              ">
                    ¿Nuevo en Fluxa?
                    <a href="{{ route('register') }}" class="link">Crear cuenta</a>
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
@vite('resources/css/auth/login.css')
@endpush

@push('scripts')
@vite('resources/js/shared/passwordVisibility.js')
@endpush