@extends('layouts.app')
@section('title', 'Verificación en dos pasos')
@section('content')

<div class="two-factor-wrapper">
    <div class="two-factor-container">

        {{-- Logo --}}
        <a href="{{ route('login') }}" style="text-align:center">
            <img src="{{ asset('img/logoFluxa.png') }}" alt="Fluxa" class="logo-img" />
        </a>

        {{-- Card principal --}}
        <div class="form-card">

            {{-- Ícono --}}
            <div class="security-icon">
                <svg width="26" height="26" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                        d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                </svg>
            </div>

            <h1 class="card-title">Verificación en dos pasos</h1>
            <p class="card-subtitle">
                Ingresa el código de 6 dígitos de tu aplicación de autenticación (Google Authenticator, Authy, etc.)
            </p>

            {{-- Formulario código TOTP --}}
            <form method="POST" action="{{ route('two-factor.login.store') }}" id="formTOTP">
                @csrf
                <div class="input-group">
                    <label class="input-label" for="code">Código de verificación</label>
                    <input
                        type="text"
                        id="code"
                        name="code"
                        class="input-otp {{ $errors->has('code') ? 'error' : '' }}"
                        placeholder="000000"
                        maxlength="6"
                        inputmode="numeric"
                        autocomplete="one-time-code"
                        autofocus />
                    @error('code')
                    <div class="error-message">
                        <svg width="14" height="14" fill="currentColor" viewBox="0 0 16 16">
                            <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z" />
                            <path d="M7.002 11a1 1 0 1 1 2 0 1 1 0 0 1-2 0zM7.1 4.995a.905.905 0 1 1 1.8 0l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 4.995z" />
                        </svg>
                        <span>{{ $message }}</span>
                    </div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary" id="btnVerify">
                    <span class="loader"></span>
                    <span class="btn-text">Verificar e ingresar</span>
                </button>
            </form>

            {{-- Recuperación --}}
            <details class="recovery-toggle">
                <summary>
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                    </svg>
                    ¿No tienes acceso a tu app? Usa un código de recuperación
                    <svg class="recovery-chevron" width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </summary>

                <form method="POST" action="{{ route('two-factor.login.store') }}">
                    @csrf
                    <input
                        type="text"
                        name="recovery_code"
                        class="input-recovery"
                        placeholder="xxxxxxxx-xxxxxxxx"
                        autocomplete="one-time-code" />
                    @error('recovery_code')
                    <div class="error-message" style="margin-bottom:0.75rem">
                        <svg width="14" height="14" fill="currentColor" viewBox="0 0 16 16">
                            <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z" />
                            <path d="M7.002 11a1 1 0 1 1 2 0 1 1 0 0 1-2 0zM7.1 4.995a.905.905 0 1 1 1.8 0l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 4.995z" />
                        </svg>
                        <span>{{ $message }}</span>
                    </div>
                    @enderror
                    <button type="submit" class="btn btn-secondary">
                        Verificar con código de recuperación
                    </button>
                </form>
            </details>
        </div>

        {{-- Volver al login --}}
        <a href="{{ route('login') }}" class="back-link">
            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Volver al inicio de sesión
        </a>

    </div>
</div>

@endsection

@push('styles')
@vite('resources/css/auth/twoFactor.css')
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const input = document.getElementById('code');
        const btn = document.getElementById('btnVerify');
        const form = document.getElementById('formTOTP');

        // Solo dígitos
        input?.addEventListener('input', () => {
            input.value = input.value.replace(/\D/g, '');
        });

        // Loading state al enviar
        form?.addEventListener('submit', () => {
            btn.classList.add('loading');
            btn.disabled = true;
        });
    });
</script>
@endpush