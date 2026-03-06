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
            <span class="active"></span>
            <span></span>
        </div>

        <h1>¿Cuál es tu rol?</h1>
        <p class="subtitle">Paso 2 de 3 · Personalizaremos tu feed según tu especialidad</p>

        <form action="{{ route('onboarding.saveRole') }}" method="POST">
            @csrf
            <div class="roles">
                @php
                $roles = [
                ['value' => 'frontend', 'name' => 'Frontend', 'desc' => 'UI, CSS, UX', 'icon' => 'devicon-html5-plain colored'],
                ['value' => 'backend', 'name' => 'Backend', 'desc' => 'APIs, bases de datos', 'icon' => 'devicon-nodejs-plain colored'],
                ['value' => 'fullstack', 'name' => 'Fullstack', 'desc' => 'Desarrollo completo', 'icon' => 'devicon-javascript-plain colored'],
                ['value' => 'devops', 'name' => 'DevOps', 'desc' => 'CI/CD, infra, cloud', 'icon' => 'devicon-docker-plain colored'],
                ['value' => 'mobile', 'name' => 'Mobile', 'desc' => 'iOS, Android, Flutter', 'icon' => 'devicon-flutter-plain colored'],
                ['value' => 'data', 'name' => 'Data / ML', 'desc' => 'Análisis, modelos IA', 'icon' => 'devicon-python-plain colored'],
                ];
                @endphp

                @foreach($roles as $role)
                <div class="role-item">
                    <input type="radio" name="role" value="{{ $role['value'] }}" id="role_{{ $loop->index }}" {{ $loop->first ? 'checked' : '' }}>
                    <label for="role_{{ $loop->index }}">
                        <div class="role-icon">
                            <i class="{{ $role['icon'] }}"></i>
                        </div>
                        <div class="role-info">
                            <span class="role-name">{{ $role['name'] }}</span>
                            <span class="role-desc">{{ $role['desc'] }}</span>
                        </div>
                        <div class="role-check"></div>
                    </label>
                </div>
                @endforeach
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
            alt="Elige tu rol">

        <p class="tagline"><span>Conecta con desarrolladores</span><br>que comparten tu especialidad</p>

        <div class="stat-cards">
            <div class="stat-card">
                <span class="num">2.4k</span>
                <span class="label">Desarrolladores</span>
            </div>
            <div class="stat-card">
                <span class="num">840+</span>
                <span class="label">Proyectos</span>
            </div>
            <div class="stat-card">
                <span class="num">6</span>
                <span class="label">Especialidades</span>
            </div>
        </div>
    </div>

</div>
@endsection

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;500;600;700&family=DM+Sans:wght@400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/devicons/devicon@latest/devicon.min.css">
<link rel="stylesheet" href="{{ asset('css/role.css') }}">
@endpush

@push('scripts')
@endpush