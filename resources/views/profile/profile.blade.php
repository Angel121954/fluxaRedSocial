@extends('layouts.app')
@section('title', 'Perfil')
@section('content')
@include('components.topbar')
<!-- ══════════════════════════════════════════
     PROFILE HEADER
══════════════════════════════════════════ -->
<div class="profile-band">
    <div class="profile-inner">
        <div class="profile-row">
            <!-- Avatar -->
            <div class="avatar-wrap" id="avatarWrap">
                <img
                    src="{{ str_replace('type=normal', 'type=large', $profile->avatar ?? '') }}"
                    alt="{{ Auth()->user()->username }}"
                    class="avatar-img"
                    id="avatarImg" />
                <div class="avatar-overlay">
                    <button
                        class="av-btn btnCam"
                        aria-label="Cambiar foto"
                        title="Cambiar foto">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </button>
                    <button
                        class="av-btn btnView"
                        aria-label="Ver foto"
                        title="Ver foto completa">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Meta -->
            <div class="profile-meta">
                <div class="meta-top">
                    <div class="meta-left">
                        <div class="name-row">
                            <h1 class="profile-name">{{ Auth::user()->name ?? '' }}</h1>
                            <!--<span class="badge-pro">PRO</span>-->
                        </div>
                        <p class="handle">&#64;{{ Auth::user()->username }}</p>
                        <p class="bio">
                            {{ Auth()->user()->profile->bio ?? '' }}
                        </p>
                        <a
                            href="{{ Auth()->user()->profile->website_url ?? '' }}"
                            class="profile-url"
                            target="_blank"
                            rel="noopener">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                            </svg>
                            {{ Auth()->user()->profile->website_url ?? '' }}
                        </a>
                    </div>

                    <!-- Actions -->
                    <div class="meta-actions">
                        <button class="btn-follow" id="btnFollow">Seguir</button>
                        <div class="drop-wrap">
                            <button
                                class="btn-icon"
                                id="btnMore"
                                aria-label="Más opciones">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M12 5v.01M12 12v.01M12 19v.01" />
                                </svg>
                            </button>
                            <div class="drop-menu" id="dropMenu">
                                <!-- Perfil propio -->
                                <div id="ownOpts">
                                    <a href="{{ route('configuration.index') }}" class="drop-item">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M11.42 15.17 17.25 21A2.652 2.652 0 0 0 21 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655 5.653a2.548 2.548 0 1 1-3.586-3.586l6.837-5.63m5.108-.233c.55-.164 1.163-.188 1.743-.14a4.5 4.5 0 0 0 4.486-6.336l-3.276 3.277a3.004 3.004 0 0 1-2.25-2.25l3.276-3.276a4.5 4.5 0 0 0-6.336 4.486c.091 1.076-.071 2.264-.904 2.95l-.102.085m-1.745 1.437L5.909 7.5H4.5L2.25 3.75l1.5-1.5L7.5 4.5v1.409l4.26 4.26m-1.745 1.437 1.745-1.437m6.615 8.206L15.75 15.75M4.867 19.125h.008v.008h-.008v-.008Z" />
                                        </svg>
                                        Configuración
                                    </a>
                                    <div class="drop-sep"></div>
                                    <button
                                        class="drop-item"
                                        onclick="compartirPerfil('{{ Auth::user()->username }}', '{{ Auth::user()->name }}')">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z" />
                                        </svg>
                                        Compartir perfil
                                    </button>

                                    <button
                                        title="¡Recuerda llenar toda la información correspondiente del perfil para mejorar el CV! :)"
                                        class="drop-item"
                                        onclick="downloadCV()">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m.75 12 3 3m0 0 3-3m-3 3v-6m-1.5-9H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                                        </svg>
                                        Descargar CV
                                    </button>

                                    <!-- Toast de confirmación -->
                                    <div id="share-toast" class="share-toast hidden">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="16" height="16">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                        <span id="share-toast-msg">¡Enlace copiado!</span>
                                    </div>
                                </div>
                                <!-- Perfil ajeno -->
                                <div id="otherOpts" style="display: none">
                                    <button class="drop-item">
                                        <svg
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z" />
                                        </svg>
                                        Compartir perfil
                                    </button>
                                    <button class="drop-item">
                                        <svg
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                        </svg>
                                        Bloquear
                                    </button>
                                    <div class="drop-sep"></div>
                                    <button class="drop-item danger">
                                        <svg
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                        </svg>
                                        Reportar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Stats -->
                <div class="stats-row">
                    <div class="stat">
                        <span class="stat-n">3</span><span class="stat-l">Proyectos</span>
                    </div>
                    <div class="stat">
                        <span class="stat-n">53</span><span class="stat-l">Siguiendo</span>
                    </div>
                    <div class="stat">
                        <span class="stat-n">220</span><span class="stat-l">Seguidores</span>
                    </div>
                    <div class="stat">
                        <span class="stat-n">46</span><span class="stat-l">Días activo</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ══════════════════════════════════════════
     TABS
══════════════════════════════════════════ -->
<div class="tabs-bar">
    <div class="tabs-inner">
        <div class="tab active" data-tab="progress">Progreso</div>
        <div class="tab" data-tab="projects">
            Proyectos <span class="tab-count">3</span>
        </div>
        <div class="tab" data-tab="stack">
            Stack <span class="tab-count">8</span>
        </div>
    </div>
</div>

<!-- ══════════════════════════════════════════
     CONTENT
══════════════════════════════════════════ -->

{{-- Panel: Progreso --}}
<div class="content" data-panel="progress">
    @forelse ($projects as $project)
    <div class="p-card">

        {{-- Cabecera --}}
        <div class="card-head">
            <div>
                <h3 class="card-title">{{ $project->title }}</h3>
                <div class="card-badges">
                    <span class="badge-day">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                        DÍA {{ $project->days_active }}
                    </span>
                </div>
            </div>
            <button class="card-menu" aria-label="Opciones del proyecto">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01" />
                </svg>
            </button>
        </div>

        {{-- Descripción --}}
        @if($project->content)
        <p class="card-desc">{{ $project->content }}</p>
        @endif

        {{-- Tecnologías --}}
        @if($project->technologies->isNotEmpty())
        <div class="tech-row">
            @foreach($project->technologies as $tech)
            <span class="t-tag">{{ $tech->name }}</span>
            @endforeach
        </div>
        @endif

        {{-- Galería dinámica desde Cloudinary --}}
        @if($project->media->isNotEmpty())
        @include('components.project-media', ['media' => $project->media])
        @endif

        {{-- Última actualización --}}
        <div class="update-block">
            <img src="{{ Auth::user()->profile->avatar ?? '' }}"
                alt="{{ Auth::user()->name }}"
                class="upd-av" />
            <div class="upd-body">
                <p class="upd-text">{{ $project->content ?? 'Sin descripción aún.' }}</p>
                <div class="upd-foot">
                    <span class="upd-time">{{ $project->updated_at->diffForHumans() }}</span>
                    <div class="upd-actions">
                        <span class="upd-act">
                            <svg fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5
                                         2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09
                                         C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22
                                         8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z" />
                            </svg>
                            {{ $project->likes_count }}
                        </span>
                        <span class="upd-act">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M8 12h.01M12 12h.01M16 12h.01M21 12c0
                                       4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949
                                       L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12
                                       c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                            </svg>
                            {{ $project->comments_count }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

    </div>
    @empty
    <p class="empty-state">Este usuario aún no tiene proyectos.</p>
    @endforelse
</div>

{{-- Panel: Proyectos --}}
<div class="content" data-panel="projects" style="display:none">
    @forelse ($projects as $project)
    <div class="p-card">
        <div class="card-head">
            <h3 class="card-title">{{ $project->title }}</h3>
        </div>
        @if($project->content)
        <p class="card-desc">{{ $project->content }}</p>
        @endif
    </div>
    @empty
    <p class="empty-state">Este usuario aún no tiene proyectos.</p>
    @endforelse
</div>

{{-- Panel: Stack --}}
<div class="content" data-panel="stack" style="display:none">
    @include('components.stack-tab', ['technologies' => $technologies])
</div>
<!-- end content -->
@include('components.modalImage')
@include('components.cv-template')

<input type="file" id="fileIn" accept="image/*" style="display: none" />
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('css/profile/profile.css') }}" />
<link rel="stylesheet" href="{{ asset('css/profile/modalImage.css') }}" />
@endpush

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<script src="{{ asset('js/profile/profileOptions.js') }}"></script>
<script src="{{ asset('js/profile/dropdown.js') }}"></script>
<script src="{{ asset('js/profile/avatar.js') }}"></script>
<script src="{{ asset('js/profile/tabs.js') }}"></script>
<script src="{{ asset('js/profile/filters.js') }}"></script>
<script src="{{ asset('js/profile/shareProfile.js') }}"></script>
<script>
    const CV_USERNAME = "{{ Auth::user()->username }}";
    const CV_NAME = "{{ Auth::user()->name }}";
</script>
<style>
    @keyframes spin {
        from {
            transform: rotate(0deg);
        }

        to {
            transform: rotate(360deg);
        }
    }
</style>
<script src="{{ asset('js/profile/downloadCV.js') }}"></script>
@endpush