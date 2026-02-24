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
            <div id="step1">
                <h1 class="card-title">¿Olvidaste tu contraseña?</h1>
                <p class="card-subtitle">
                    Ingresa el email asociado a tu cuenta y te enviaremos un enlace para restablecerla.
                </p>

                <form id="forgot-form" onsubmit="handleSubmit(event)">
                    <div class="form-group">
                        <label for="email">Email</label>
                        <div class="input-wrap">
                            <span class="input-icon">
                                {{-- Icono SVG de email inline (no depende de assets) --}}
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

            <!-- STEP 2: confirmación enviada (oculto por defecto) -->
            <div id="step2" style="display:none;">
                <div class="success-state">
                    <div class="success-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" />
                        </svg>
                    </div>
                    <h3>¡Correo enviado!</h3>
                    <p>Revisá tu bandeja de entrada en <strong id="sent-email"></strong> y seguí el enlace para restablecer tu contraseña.</p>
                    <p class="resend-text">¿No llegó? <a href="#" onclick="resend(event)">Reenviar correo</a></p>
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
        <img src="{{ asset('img/desarrolladorRegistro.png') }}" alt="Ilustración desarrollador" />
    </div>

</main>
@endsection

@push('scripts')
<script>
    function handleSubmit(e) {
        e.preventDefault();
        const email = document.getElementById('email').value;
        document.getElementById('sent-email').textContent = email;
        document.getElementById('step1').style.display = 'none';
        document.getElementById('step2').style.display = 'block';
    }

    function resend(e) {
        e.preventDefault();
        const link = e.target;
        link.textContent = '¡Enviado!';
        link.style.pointerEvents = 'none';
        setTimeout(() => {
            link.textContent = 'Reenviar correo';
            link.style.pointerEvents = '';
        }, 4000);
    }
</script>
@endpush

@push('styles')
<link rel="stylesheet" href="{{ asset('css/forgotPassword.css') }}">
@endpush