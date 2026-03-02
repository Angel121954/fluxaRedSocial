@extends('layouts.app')
@section('title', 'Configuración')
@section('content')
@include('components.topbar')
<!-- ══════════════════════════════════════════
     EDIT LAYOUT
══════════════════════════════════════════ -->
<div class="edit-layout">
    @include('components.sidebar')

    <!-- ──── MAIN CONTENT ──── -->
    <main class="main-content">
        <h1 class="page-title">Editar perfil</h1>
        <p class="page-subtitle">
            Actualiza tu información personal y cómo apareces en Fluxa
        </p>

        <form id="editForm" method="POST" action="{{ route('configuration.edit') }}">
            @csrf
            @method('PATCH')
            <!-- Nombre -->
            <div class="form-group">
                <label class="form-label" for="inputName">Nombre completo</label>
                <input
                    name="name"
                    type="text"
                    class="form-input"
                    id="inputName"
                    value="{{ old('name', Auth::user()->name ?? '') }}"
                    placeholder="Tu nombre completo" />
            </div>

            <!-- Handle -->
            <div class="form-group">
                <label class="form-label" for="inputHandle">Nombre de usuario</label>
                <span class="form-hint">Tu URL será fluxa.com/@tunombre</span>
                <input
                    name="username"
                    type="text"
                    class="form-input"
                    id="inputHandle"
                    value="{{ old('username', Auth::user()->username ?? '') }}"
                    placeholder="tunombre" />
            </div>

            <!-- Bio -->
            <div class="form-group">
                <label class="form-label" for="inputBio">Biografía</label>
                <span class="form-hint">Cuéntanos sobre ti en pocas palabras</span>
                <textarea
                    name="bio"
                    class="form-input"
                    id="inputBio"
                    maxlength="160"
                    placeholder="Full stack developer...">{{ Auth()->user()->profile->bio ?? '' }}</textarea>
                <div class="char-count" id="charCount">0/160</div>
            </div>

            <!-- Ubicación -->
            <div class="form-group">
                <label class="form-label" for="inputLocation">Ubicación</label>
                <select class="form-input" id="inputLocation" name="location">
                    @php
                    $currentLocation = old('location', Auth()->user()->profile->location ?? 'pereira')
                    @endphp
                    <option value="bogotá" {{ $currentLocation == 'bogotá' ? 'selected' : '' }}>Bogotá</option>
                    <option value="medellín" {{ $currentLocation == 'medellín' ? 'selected' : '' }}>Medellín</option>
                    <option value="cali" {{ $currentLocation == 'cali' ? 'selected' : '' }}>Cali</option>
                    <option value="barranquilla" {{ $currentLocation == 'barranquilla' ? 'selected' : '' }}>Barranquilla</option>
                    <option value="cartagena" {{ $currentLocation == 'cartagena' ? 'selected' : '' }}>Cartagena</option>
                    <option value="cúcuta" {{ $currentLocation == 'cúcuta' ? 'selected' : '' }}>Cúcuta</option>
                    <option value="bucaramanga" {{ $currentLocation == 'bucaramanga' ? 'selected' : '' }}>Bucaramanga</option>
                    <option value="pereira" {{ $currentLocation == 'pereira' ? 'selected' : '' }}>Pereira</option>
                    <option value="manizales" {{ $currentLocation == 'manizales' ? 'selected' : '' }}>Manizales</option>
                    <option value="armenia" {{ $currentLocation == 'armenia' ? 'selected' : '' }}>Armenia</option>
                    <option value="ibagué" {{ $currentLocation == 'ibagué' ? 'selected' : '' }}>Ibagué</option>
                    <option value="villavicencio" {{ $currentLocation == 'villavicencio' ? 'selected' : '' }}>Villavicencio</option>
                    <option value="santa marta" {{ $currentLocation == 'santa marta' ? 'selected' : '' }}>Santa Marta</option>
                    <option value="neiva" {{ $currentLocation == 'neiva' ? 'selected' : '' }}>Neiva</option>
                    <option value="popayán" {{ $currentLocation == 'popayán' ? 'selected' : '' }}>Popayán</option>
                    <option value="sincelejo" {{ $currentLocation == 'sincelejo' ? 'selected' : '' }}>Sincelejo</option>
                    <option value="riohacha" {{ $currentLocation == 'riohacha' ? 'selected' : '' }}>Riohacha</option>
                    <option value="quibdó" {{ $currentLocation == 'quibdó' ? 'selected' : '' }}>Quibdó</option>
                    <option value="yopal" {{ $currentLocation == 'yopal' ? 'selected' : '' }}>Yopal</option>
                    <option value="leticia" {{ $currentLocation == 'leticia' ? 'selected' : '' }}>Leticia</option>
                </select>
            </div>

            <!-- Website -->
            <div class="form-group">
                <label class="form-label" for="inputWebsite">Sitio web</label>
                <div class="input-with-button">
                    <input
                        name="website_url"
                        type="url"
                        class="form-input"
                        id="inputWebsite"
                        value="{{ old('website_url', Auth()->user()->profile->website_url ?? '') }}"
                        placeholder="https://tusitio.com" />
                    <a
                        href="{{ Auth()->user()->profile->website_url ?? '#' }}"
                        target="_blank"
                        class="btn-link">
                        <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                        </svg>
                        Visitar
                    </a>
                </div>
            </div>

            <!-- ══════════════════════════════════════════
                 REDES SOCIALES
            ══════════════════════════════════════════ -->
            <div class="form-section-title">
                <h2>Redes sociales</h2>
                <p class="page-subtitle">Conecta tus perfiles para que otros puedan encontrarte</p>
            </div>

            <!-- GitHub -->
            <div class="form-group">
                <label class="form-label" for="inputGithub">GitHub</label>
                <div class="input-with-icon">
                    <svg class="input-icon" width="18" height="18" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 0C5.37 0 0 5.37 0 12c0 5.3 3.438 9.8 8.205 11.385.6.113.82-.258.82-.577 0-.285-.01-1.04-.015-2.04-3.338.724-4.042-1.61-4.042-1.61-.546-1.385-1.335-1.755-1.335-1.755-1.087-.744.084-.729.084-.729 1.205.084 1.838 1.236 1.838 1.236 1.07 1.835 2.809 1.305 3.495.998.108-.776.417-1.305.76-1.605-2.665-.3-5.466-1.332-5.466-5.93 0-1.31.465-2.38 1.235-3.22-.135-.303-.54-1.523.105-3.176 0 0 1.005-.322 3.3 1.23.96-.267 1.98-.399 3-.405 1.02.006 2.04.138 3 .405 2.28-1.552 3.285-1.23 3.285-1.23.645 1.653.24 2.873.12 3.176.765.84 1.23 1.91 1.23 3.22 0 4.61-2.805 5.625-5.475 5.92.42.36.81 1.096.81 2.22 0 1.606-.015 2.896-.015 3.286 0 .315.21.69.825.57C20.565 21.795 24 17.295 24 12c0-6.63-5.37-12-12-12" />
                    </svg>
                    <input
                        name="github_url"
                        type="url"
                        class="form-input form-input--icon"
                        id="inputGithub"
                        value="{{ old('github_url', Auth()->user()->profile->github_url ?? '') }}"
                        placeholder="https://github.com/tuusuario" />
                </div>
            </div>

            <!-- Twitter / X -->
            <div class="form-group">
                <label class="form-label" for="inputTwitter">Twitter / X</label>
                <div class="input-with-icon">
                    <svg class="input-icon" width="18" height="18" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-4.714-6.231-5.401 6.231H2.748l7.73-8.835L1.254 2.25H8.08l4.253 5.622 5.91-5.622zm-1.161 17.52h1.833L7.084 4.126H5.117z" />
                    </svg>
                    <input
                        name="twitter_url"
                        type="url"
                        class="form-input form-input--icon"
                        id="inputTwitter"
                        value="{{ old('twitter_url', Auth()->user()->profile->twitter_url ?? '') }}"
                        placeholder="https://twitter.com/tuusuario" />
                </div>
            </div>

            <!-- LinkedIn -->
            <div class="form-group">
                <label class="form-label" for="inputLinkedin">LinkedIn</label>
                <div class="input-with-icon">
                    <svg class="input-icon" width="18" height="18" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433a2.062 2.062 0 01-2.063-2.065 2.064 2.064 0 112.063 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z" />
                    </svg>
                    <input
                        name="linkedin_url"
                        type="url"
                        class="form-input form-input--icon"
                        id="inputLinkedin"
                        value="{{ old('linkedin_url', Auth()->user()->profile->linkedin_url ?? '') }}"
                        placeholder="https://linkedin.com/in/tuusuario" />
                </div>
            </div>

            <!-- ══════════════════════════════════════════
                 INFORMACIÓN PERSONAL
            ══════════════════════════════════════════ -->
            <div class="form-section-title">
                <h2>Información personal</h2>
                <p class="page-subtitle">Esta información es opcional y puedes controlar quién la ve desde Privacidad</p>
            </div>

            <!-- Fecha de nacimiento -->
            <div class="form-group">
                <label class="form-label" for="inputBirthDate">Fecha de nacimiento</label>
                <input
                    type="date"
                    class="form-input"
                    id="inputBirthDate"
                    name="birth_date"
                    value="{{ old('birth_date', Auth()->user()->profile->birth_date ?? '') }}" />
            </div>

            <!-- Género -->
            <div class="form-group">
                <label class="form-label" for="inputGender">Género</label>
                @php
                $currentGender = old('gender', Auth()->user()->profile->gender ?? '')
                @endphp
                <select class="form-input" id="inputGender" name="gender">
                    <option value="" {{ $currentGender == '' ? 'selected' : '' }} disabled>Selecciona una opción</option>
                    <option value="male" {{ $currentGender == 'male' ? 'selected' : '' }}>Masculino</option>
                    <option value="female" {{ $currentGender == 'female' ? 'selected' : '' }}>Femenino</option>
                    <option value="other" {{ $currentGender == 'other' ? 'selected' : '' }}>Otro / Prefiero no decir</option>
                </select>
            </div>

            <!-- Mensaje de éxito -->
            @if (session('success'))
            <div class="alert alert-success" id="alertMessage">
                <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M5 13l4 4L19 7" />
                </svg>
                {{ session('success') }}
            </div>
            @endif

            <!-- Mensaje de error -->
            @if ($errors->any())
            <div class="alert alert-error" id="alertMessage">
                <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M6 18L18 6M6 6l12 12" />
                </svg>
                {{ $errors->first() }}
            </div>
            @endif

            <!-- Actions -->
            <div class="form-actions">
                <button
                    type="button"
                    class="btn-cancel"
                    onclick="window.location.href = '/profile'">
                    Cancelar
                </button>
                <button type="submit" class="btn-submit">Guardar cambios</button>
            </div>
        </form>
    </main>
</div>

<input type="file" id="fileInput" accept="image/*" style="display: none" />
@endsection
@push('styles')
<!--Estilo personalizado de configuración-->
<link rel="stylesheet" href="{{ asset('css/configuration.css') }}" />
@endpush
@push('scripts')
<script src="{{ asset('js/configuration.js') }}"></script>
@endpush