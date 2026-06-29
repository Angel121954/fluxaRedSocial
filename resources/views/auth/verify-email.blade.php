@extends('layouts.app')
@section('content')

<div class="login-screen">
    <div class="login-wrapper">

        <div class="login-card">

            <div class="login-brand">
                <x-header />
            </div>

            <h1 class="login-title">Verifica tu correo electrónico</h1>
            <p class="login-subtitle">
                Gracias por registrarte. Antes de empezar, verifica tu dirección de correo
                con el enlace que te enviamos. Si no lo recibiste, podemos enviarte otro.
            </p>

            @if (session('status') == 'verification-link-sent')
            <div class="notification show">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="20 6 9 17 4 12" />
                </svg>
                <span>Se ha enviado un nuevo enlace de verificación a tu correo.</span>
            </div>
            @endif

            <div class="login-form" style="margin-top:1.5rem">
                <form method="POST" action="{{ route('verification.send') }}">
                    @csrf
                    <button type="submit" class="btn-primary btn-primary--full">
                        Reenviar correo de verificación
                    </button>
                </form>

                <form method="POST" action="{{ route('logout') }}" style="margin-top:0.75rem">
                    @csrf
                    <button type="submit" class="btn-text">
                        Cerrar sesión
                    </button>
                </form>
            </div>

        </div>

        <p class="login-footer">© {{ date('Y') }} Fluxa. Todos los derechos reservados.</p>

    </div>
</div>

@endsection

@push('styles')
@vite('resources/css/auth/login.css')
@endpush
