@extends('layouts.app')
@section('content')
<!-- MAIN -->
<main class="main">

    <!-- LEFT -->
    <div class="left-panel">

        <div class="intro">
            <p>Únete a la red social para desarrolladores del SENA que construyen en público.</p>
        </div>

        <!-- CARD -->
        <div class="card">

            <!-- STEP 1: nueva contraseña -->
            <div id="step1">
                <h1 class="card-title">Restablecer contraseña</h1>
                <p class="card-subtitle">
                    Ingresa y confirma tu nueva contraseña para recuperar el acceso a tu cuenta.
                </p>

                <form id="reset-form" method="POST" action="{{ route('password.store') }}">
                    @csrf

                    <!-- Token oculto -->
                    <input type="hidden" name="token" value="{{ $request->route('token') }}">

                    <!-- Email -->
                    <div class="form-group">
                        <label for="email">Email</label>
                        <div class="input-wrap">
                            <span class="input-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" />
                                </svg>
                            </span>
                            <input
                                type="email"
                                id="email"
                                name="email"
                                value="{{ old('email', $request->email) }}"
                                placeholder="tucorreo@ejemplo.com"
                                required
                                autocomplete="email" />
                        </div>
                        @error('email')
                        <span class="field-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Nueva contraseña -->
                    <div class="form-group">
                        <label for="password">Nueva contraseña</label>
                        <div class="input-wrap">
                            <span class="input-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
                                </svg>
                            </span>
                            <input
                                type="password"
                                id="password"
                                name="password"
                                placeholder="Mínimo 8 caracteres"
                                required
                                autocomplete="new-password" />
                            <button type="button" class="toggle-password" onclick="togglePassword('password', this)" tabindex="-1">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                </svg>
                            </button>
                        </div>

                        <!-- Indicador de fortaleza -->
                        <div class="password-strength" id="strength-bar">
                            <div class="strength-track">
                                <div class="strength-fill" id="strength-fill"></div>
                            </div>
                            <span class="strength-label" id="strength-label"></span>
                        </div>

                        @error('password')
                        <span class="field-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Confirmar contraseña -->
                    <div class="form-group">
                        <label for="password_confirmation">Confirmar contraseña</label>
                        <div class="input-wrap">
                            <span class="input-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z" />
                                </svg>
                            </span>
                            <input
                                type="password"
                                id="password_confirmation"
                                name="password_confirmation"
                                placeholder="Repite tu nueva contraseña"
                                required
                                autocomplete="new-password" />
                            <button type="button" class="toggle-password" onclick="togglePassword('password_confirmation', this)" tabindex="-1">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                </svg>
                            </button>
                        </div>
                        @error('password_confirmation')
                        <span class="field-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <button type="submit" class="btn-primary">
                        Restablecer contraseña
                    </button>
                </form>

                <a href="{{ route('login') }}" class="back-link">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                        stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M6.75 15.75 3 12m0 0 3.75-3.75M3 12h18" />
                    </svg>
                    Volver a Iniciar sesión
                </a>
            </div>

            <!-- STEP 2: éxito -->
            <div id="step2" style="display:none;">
                <div class="success-state">
                    <div class="success-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                        </svg>
                    </div>
                    <h3>¡Contraseña actualizada!</h3>
                    <p>Tu contraseña fue restablecida exitosamente. Ya podés iniciar sesión con tu nueva contraseña.</p>
                </div>

                <a href="{{ route('login') }}" class="btn-primary" style="display:block; text-align:center; text-decoration:none; margin-top: 1rem;">
                    Ir a Iniciar sesión
                </a>
            </div>

        </div>
    </div>

    <!-- RIGHT -->
    <div class="right-panel">
        <img src="{{ asset('img/imagenEmail.png') }}" alt="Ilustración desarrollador" />
    </div>

</main>
@endsection

@push('scripts')
@vite('resources/js/shared/securePassword.js')
@endpush

@push('styles')
@vite('resources/css/forgotPassword.css')
@vite('resources/css/resetPassword.css')
@endpush