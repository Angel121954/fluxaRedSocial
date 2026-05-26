@extends('layouts.app')

@section('content')
<div class="page">

    <!-- LEFT -->
    <div class="left">

        <a href="#" class="logo">
            <img src="{{ asset('img/logoFluxa.png') }}" alt="Fluxa">
        </a>

        <div class="progress-bar">
            <span class="done"></span>
            <span class="done"></span>
            <span class="done"></span>
            <span class="active"></span>
        </div>

        <h1>Cuéntanos sobre ti</h1>
        <p class="subtitle">Paso 3 de 4 · Escribe una breve biografía para que otros desarrolladores te conozcan</p>

        <form action="{{ route('onboarding.saveBio') }}" method="POST" id="bioForm">
            @csrf
            <div class="bio-field">
                <textarea
                    name="bio"
                    id="bioInput"
                    maxlength="400"
                    rows="5"
                    placeholder="Ej: Desarrollador fullstack apasionado por Laravel y Vue. Me encanta construir herramientas open-source y compartir conocimiento con la comunidad."
                >{{ old('bio') }}</textarea>
                <div class="bio-counter">
                    <span id="bioCount">0</span>/400
                </div>
            </div>

            <button type="submit" class="btn">Continuar →</button>
        </form>

        <p class="skip"><a href="{{ route('onboarding.suggestions') }}">Omitir por ahora</a></p>
    </div>

    <!-- RIGHT -->
    <div class="right">
        <img
            class="illustration"
            src="{{ asset('img/desarrolladorRegistro.png') }}"
            alt="Cuéntanos sobre ti">

        <p class="tagline"><span>Tu historia importa</span><br>Comparte quién eres con la comunidad</p>

        <div class="badges">
            <span class="badge">Conecta con otros devs</span>
            <span class="badge">Construye tu marca</span>
            <span class="badge">Muestra tu experiencia</span>
        </div>
    </div>

</div>
@endsection

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;500;600;700&family=DM+Sans:wght@400;500&display=swap" rel="stylesheet">
@vite('resources/css/onboarding/bio.css')
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var input = document.getElementById('bioInput');
        var count = document.getElementById('bioCount');

        if (input && count) {
            count.textContent = input.value.length;

            input.addEventListener('input', function() {
                count.textContent = this.value.length;
            });
        }
    });
</script>
@endpush
