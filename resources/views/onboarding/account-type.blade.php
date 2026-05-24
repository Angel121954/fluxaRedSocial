@extends('layouts.app')

@section('content')
<div class="page">

    <!-- LEFT -->
    <div class="left">

        <a href="#" class="logo">
            <img src="{{ asset('img/logoFluxa.png') }}" alt="Fluxa">
        </a>

        <h1>¿Qué tipo de cuenta quieres crear?</h1>
        <p class="subtitle">Elige el tipo de cuenta que mejor se ajuste a tu perfil profesional</p>

        <form action="{{ route('onboarding.saveAccountType') }}" method="POST">
            @csrf
            <div class="account-types">
                <div class="atype-item">
                    <input type="radio" name="account_type" value="developer" id="atype_developer">
                    <label for="atype_developer">
                        <div class="atype-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="16 18 22 12 16 6" />
                                <polyline points="8 6 2 12 8 18" />
                                <line x1="14" y1="4" x2="10" y2="20" />
                            </svg>
                        </div>
                        <div class="atype-info">
                            <span class="atype-name">Desarrollador</span>
                            <span class="atype-desc">Comparte proyectos, colabora con otros devs y construye tu marca profesional</span>
                        </div>
                        <div class="atype-check"></div>
                    </label>
                </div>

                <!-- <div class="atype-item">
                    <input type="radio" name="account_type" value="company" id="atype_company">
                    <label for="atype_company">
                        <div class="atype-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="2" y="8" width="20" height="14" rx="2" />
                                <path d="M16 8V6a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2" />
                                <line x1="12" y1="12" x2="12.01" y2="12" />
                                <line x1="8" y1="12" x2="8.01" y2="12" />
                                <line x1="16" y1="12" x2="16.01" y2="12" />
                            </svg>
                        </div>
                        <div class="atype-info">
                            <span class="atype-name">Empresa</span>
                            <span class="atype-desc">Publica ofertas de empleo, encuentra talento y muestra tu cultura empresarial</span>
                        </div>
                        <div class="atype-check"></div>
                    </label>
                </div> -->
            </div>

            <button type="submit" class="btn" id="btnContinue" disabled>Continuar →</button>
        </form>

        <p class="note">Podrás cambiar esta configuración más adelante desde Ajustes</p>
    </div>

    <!-- RIGHT -->
    <div class="right">
        <img
            class="illustration"
            src="{{ asset('img/desarrolladorRegistro.png') }}"
            alt="Tipo de cuenta">

        <p class="tagline"><span>Fluxa para todos</span><br>Desarrolladores y empresas en un solo lugar</p>

        <div class="badges">
            <span class="badge">Comparte proyectos</span>
            <span class="badge">Encuentra talento</span>
            <span class="badge">Publica ofertas</span>
        </div>
    </div>

</div>

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;500;600;700&family=DM+Sans:wght@400;500&display=swap" rel="stylesheet">
@vite('resources/css/onboarding/account-type.css')
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var radios = document.querySelectorAll('input[name="account_type"]');
        var btn = document.getElementById('btnContinue');
        radios.forEach(function(r) {
            r.addEventListener('change', function() {
                btn.disabled = false;
            });
        });
    });
</script>
@endpush
@endsection