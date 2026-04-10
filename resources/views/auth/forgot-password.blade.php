@extends('layouts.app')
@section('content')

<!-- TOP NAV -->
<nav class="topbar">
    <a href="{{ route('login') }}" class="logo">
        <img src="{{ asset('img/logoFluxa.png') }}" alt="Logo Fluxa" />
    </a>
</nav>

<!-- MAIN -->
<main class="main">

    <!-- LEFT -->
    <div class="left-panel">

        <div class="intro">
            <p>Únete a la red social para desarrolladores del SENA que construyen en público.</p>
        </div>

        <!-- CARD -->
        <div class="card">

            <!-- STEP 1: ingresar email -->
            <div id="step1" {{ session('status') ? 'style=display:none' : '' }}>
                <h1 class="card-title">¿Olvidaste tu contraseña?</h1>
                <p class="card-subtitle">
                    Ingresa el email asociado a tu cuenta y te enviaremos un enlace para restablecerla.
                </p>

                {{-- Mostrar error si el email no existe --}}
                @error('email')
                <div class="alert-error">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                        stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                    </svg>
                    {{ $message }}
                </div>
                @enderror

                <form method="POST" action="{{ route('password.email') }}">
                    @csrf
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
                                value="{{ old('email') }}"
                                type="email"
                                id="email"
                                name="email"
                                placeholder="tucorreo@ejemplo.com"
                                required />
                        </div>
                    </div>

                    <button type="submit" class="btn-primary">
                        Enviar enlace de recuperación
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

            <!-- STEP 2: confirmación enviada -->
            <div id="step2" {{ session('status') ? '' : 'style=display:none' }}>
                <div class="success-state">
                    <div class="success-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" />
                        </svg>
                    </div>
                    <h3>¡Correo enviado!</h3>
                    <p>
                        Revisá tu bandeja de entrada en
                        <strong>{{ old('email') }}</strong>
                        y seguí el enlace para restablecer tu contraseña.
                    </p>
                    <p class="resend-text">
                        ¿No llegó?
                        <a href="#" id="resend-link" onclick="resend(event)">Reenviar correo</a>
                    </p>
                </div>

                <a href="{{ route('login') }}" class="back-link">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                        stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M6.75 15.75 3 12m0 0 3.75-3.75M3 12h18" />
                    </svg>
                    Volver a Iniciar sesión
                </a>
            </div>

        </div>
    </div>

    <!-- RIGHT -->
    <div class="right-panel">
        <img src="{{ asset('img/imagenRecuperarContrasena.png') }}" alt="Ilustración desarrollador" />
    </div>

</main>
@endsection

@push('scripts')
@vite('resources/js/shared/emailModalSend.js')
@endpush

@push('styles')
@vite('resources/css/forgotPassword.css')
@endpush