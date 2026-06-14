@extends('layouts.app')
@section('title', $user->name ?? 'Perfil')
@section('content')
<x-topbar :profile="$user->profile" />

<body class="font-sans antialiased" data-user-avatar="{{ $user->avatar_url }}" data-user-name="{{ $user->name }}" data-user-handle="{{ $user->username }}">
    <!-- ══════════════════════════════════════════
     PROFILE HEADER
══════════════════════════════════════════ -->
    <div class="profile-band">
        <div class="profile-inner">
            <div class="profile-row">
                <!-- Avatar -->
                <div class="avatar-wrap" id="avatarWrap">
                    <img
                        src="{{ str_replace('type=normal', 'type=large', $user->avatar_url) }}"
                        alt="{{ $user->username }}"
                        class="avatar-img"
                        id="avatarImg" />

                    <div class="avatar-overlay">
                        @if(isset($isOwner) && $isOwner)
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
                        @endif
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
                                <h1 class="profile-name">{{ $user->name }}</h1>
                            </div>
                            <p class="handle">&#64;{{ $user->username }}</p>
                            <p class="bio">
                                {{ $profile->bio ?? '' }}
                                <br>
                                {{ $profile->show_email ? $user->email : '' }}
                            </p>
                            @if($profile->website_url)
                            <a
                                href="{{ $profile->website_url }}"
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
                                {{ $profile->website_url }}
                            </a>
                            @endif
                        </div>

                        <!-- Actions -->
                        <div class="meta-actions" data-is-owner="{{ json_encode($isOwner) }}" data-is-following="{{ json_encode($isFollowing ?? false) }}" data-is-followed-by="{{ json_encode($isFollowedBy ?? false) }}" data-profile-user-id="{{ $user->id }}">
                            @if(isset($isOwner) && $isOwner)
                            <div class="drop-wrap" data-is-owner="true">
                                <button class="btn-icon" id="btnMore" aria-label="Más opciones">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01" />
                                    </svg>
                                </button>
                                <div class="drop-menu" id="dropMenu">
                                    <div id="ownOpts">
                                        <a href="{{ route('configuration.index') }}" class="drop-item">
                                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="16" height="16">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.42 15.17 17.25 21A2.652 2.652 0 0 0 21 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655 5.653a2.548 2.548 0 1 1-3.586-3.586l6.837-5.63m5.108-.233c.55-.164 1.163-.188 1.743-.14a4.5 4.5 0 0 0 4.486-6.336l-3.276 3.277a3.004 3.004 0 0 1-2.25-2.25l3.276-3.276a4.5 4.5 0 0 0-6.336 4.486c.091 1.076-.071 2.264-.904 2.95l-.102.085m-1.745 1.437L5.909 7.5H4.5L2.25 3.75l1.5-1.5L7.5 4.5v1.409l4.26 4.26m-1.745 1.437 1.745-1.437m6.615 8.206L15.75 15.75M4.867 19.125h.008v.008h-.008v-.008Z" />
                                            </svg>
                                            Editar perfil
                                        </a>
                                        <button class="drop-item" onclick="compartirPerfil('{{ $user->username }}', '{{ $user->name }}')">
                                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z" />
                                            </svg>
                                            Compartir perfil
                                        </button>
                                        <a href="{{ route('cv.download.public') }}" title="¡Recuerda llenar toda la información correspondiente del perfil para mejorar el CV! :)" class="drop-item">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m.75 12 3 3m0 0 3-3m-3 3v-6m-1.5-9H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                                            </svg>
                                            Descargar CV
                                        </a>
                                        <div class="drop-sep"></div>
                                        <button class="drop-item" id="btnImportGitHub"
                                            data-has-token="{{ $user->github_token ? 'true' : 'false' }}"
                                            data-api-repos-url="{{ route('github.repos.list') }}"
                                            data-api-import-url="{{ route('github.repos.import') }}"
                                            data-connect-url="{{ route('github.connect') }}">
                                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.531 1.032 1.531 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844a9.59 9.59 0 012.504.338c1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.02 10.02 0 0022 12.017C22 6.484 17.522 2 12 2z" />
                                            </svg>
                                            <span>Importar desde GitHub</span>
                                        </button>
                                        <div id="share-toast" class="share-toast hidden">
                                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="16" height="16">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                            </svg>
                                            <span id="share-toast-msg">¡Enlace copiado!</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @else
                            @php
                            $btnText = $isFollowing ? 'Siguiendo' : ($isFollowedBy ? 'Seguir también' : 'Seguir');
                            @endphp
                            <div class="drop-wrap" data-is-owner="false">
                                <button class="btn-icon" id="btnMore" aria-label="Más opciones">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01" />
                                    </svg>
                                </button>
                                <div class="drop-menu" id="dropMenu">
                                    <div id="otherOpts">
                                        @auth
                                        @if($profile->accept_messages)
                                        <a href="{{ isset($conversation) ? route('messages.index', ['conv' => $conversation->id]) : route('messages.chat', $user->username) }}"
                                            class="drop-item">
                                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                            </svg>
                                            Mensaje
                                        </a>
                                        @endif
                                        @endauth
                                        <button class="drop-item{{ $isFollowing ? ' is-following' : '' }}" id="btnFollow" data-user-id="{{ $user->id }}">
                                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0ZM4 19.235v-.11a6.375 6.375 0 0 1 12.75 0v.109A12.318 12.318 0 0 1 10.374 21c-2.331 0-4.512-.645-6.374-1.766Z" />
                                            </svg>
                                            <span id="btnFollowText">{{ $btnText }}</span>
                                        </button>
                                        <div class="drop-sep"></div>
                                        <button class="drop-item" onclick="compartirPerfil('{{ $user->username }}', '{{ $user->name }}')">
                                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z" />
                                            </svg>
                                            Compartir perfil
                                        </button>
                                        <a href="{{ route('cv.download.public', ['username' => $user->username]) }}" class="drop-item" target="_blank">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m.75 12 3 3m0 0 3-3m-3 3v-6m-1.5-9H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                                            </svg>
                                            Descargar CV
                                        </a>
                                        <div class="drop-sep"></div>
                                        <button class="drop-item danger">
                                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                            </svg>
                                            Reportar
                                        </button>
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Stats -->
                    <div class="stats-row">
                        <div class="stat stat-clickable" onclick="openProjectsModal({{ $user->id }})" title="Ver proyectos">
                            <span class="stat-n">{{ $projectsCount ?? $projects->count() }}</span><span class="stat-l">Proyectos</span>
                        </div>
                        <div class="stat stat-clickable" onclick="openFollowersModal({{ $user->id }}, 'following')" title="Ver usuarios que sigue">
                            <span class="stat-n">{{ $user->follows_count }}</span><span class="stat-l">Siguiendo</span>
                        </div>
                        <div class="stat stat-clickable" onclick="openFollowersModal({{ $user->id }}, 'followers')" title="Ver seguidores">
                            <span class="stat-n">{{ $user->followers_count }}</span><span class="stat-l">Seguidores</span>
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
            <div class="tab" data-tab="projects">Proyectos</div>
            <div class="tab" data-tab="stack">Stack</div>
            <div class="tab" data-tab="badges">Logros</div>
            @if($isOwner || $profile->show_favorites)
            <div class="tab" data-tab="favorites">Favoritos</div>
            @endif
        </div>
    </div>

    <!-- ══════════════════════════════════════════
     CONTENT
══════════════════════════════════════════ -->

    {{-- Panel: Progreso --}}
    <div class="content" data-panel="progress">
        @php $typeLabels = [
        'project' => 'Proyecto',
        'work' => 'Experiencia',
        'education' => 'Educación',
        'badge' => 'Insignia',
        ]; @endphp

        <div class="activity-feed">
            @forelse ($timeline as $entry)
            @php $type = $entry['type']; $data = $entry['data']; @endphp
            <div class="act-card">
                <div class="act-head">
                    <span class="act-tag">{{ $typeLabels[$type] }}</span>
                    <span class="act-time">{{ $entry['date']->diffForHumans() }}</span>
                </div>

                @switch($type)
                @case('project')
                <strong class="act-title">{{ $data->title }}</strong>
                @if($data->content)
                <p class="act-desc">{{ Str::limit($data->content, 120) }}</p>
                @endif
                @break

                @case('work')
                <strong class="act-title">{{ $data->position }}</strong>
                <span class="act-sub">{{ $data->company }}</span>
                @break

                @case('education')
                <strong class="act-title">{{ $data->degree ?: 'Estudios' }}</strong>
                <span class="act-sub">{{ $data->institution }}</span>
                @break

                @case('badge')
                <strong class="act-title">{{ $data->name }}</strong>
                <span class="act-sub">{{ $data->description }}</span>
                @break
                @endswitch
            </div>
            @empty
            <p class="empty-state">Este usuario aún no tiene actividad registrada.</p>
            @endforelse
        </div>
    </div>

    {{-- Panel: Proyectos --}}
    <div class="content" data-panel="projects" style="display:none">
        @forelse ($projects as $project)
        <x-project-card :project="$project" />
        @empty
        <p class="empty-state">Este usuario aún no tiene proyectos.</p>
        @endforelse
    </div>

    {{-- Panel: Stack --}}
    <div class="content" data-panel="stack" style="display:none">
        <x-stack-tab
            :technologies="$technologies"
            :isOwner="$isOwner"
            :groupedTechnologies="$groupedTechnologies"
            :categoryLabels="$categoryLabels"
            :categoryOrder="$categoryOrder" />
    </div>

    {{-- Panel: Logros --}}
    <div class="content" data-panel="badges" style="display:none">
        <x-badge-grid :badges="$badges ?? collect()" :all-badges="$allBadges ?? collect()" :is-owner="$isOwner" />
        @if($isOwner)
        <x-badges-modal
            :badges="$badges ?? collect()"
            :all-badges="$allBadges ?? collect()"
            :badgeCategories="$badgeCategories"
            :tierLabels="$tierLabels" />
        @endif
    </div>

    {{-- Panel: Favoritos --}}
    @if($isOwner || $profile->show_favorites)
    <div class="content" data-panel="favorites" style="display:none">
        @if(isset($favoriteProjects) && $favoriteProjects->count() > 0)
        @foreach($favoriteProjects as $project)
        <a href="/projects/{{ $project->id }}">
            <div class="p-card">
                <div class="card-head">
                    <h3 class="card-title">{{ $project->title }}</h3>
                </div>
                @if($project->content)
                <p class="card-desc">{{ $project->content }}</p>
                @endif
                @if($project->technologies->isNotEmpty())
                <div class="tech-row">
                    @foreach($project->technologies as $tech)
                    <span class="t-tag">{{ $tech->name }}</span>
                    @endforeach
                </div>
                @endif
            </div>
        </a>
        @endforeach
        @else
        <p class="empty-state">No hay proyectos favoritos aún.</p>
        @endif
    </div>
    @endif
    <!-- end content -->
    <x-modal-image :profile="$profile" />
    @if($isOwner)
    @include('profile.partials.stack-modal')
    @endif
    <x-cv-template
        :profile="$profile"
        :user="$user"
        :technologies="$technologies"
        :projects="$projects"
        :work-experiences="$workExperiences"
        :educations="collect([])"
        :avatar-base64="null"
        :logo-base64="null"
        :qr-base64="null"
        :cv-settings="$profile->cv_settings ?? ['show_photo'=>true,'show_location'=>true,'show_email'=>true,'show_projects'=>true,'show_experience'=>true,'show_education'=>true,'section_order'=>['experience','projects','education']]" />

    <input type="file" id="fileIn" accept="image/*" style="display: none" />
    <x-modal-comments />
    <x-modal-report />
    <x-followers-modal />
    <x-projects-modal />
    @include('profile.partials.github-import-modal')
</body>
@endsection

@push('styles')
@vite('resources/css/shared/modal.css')
@vite('resources/css/core/explore.css')
@vite('resources/css/profile/band.css')
@vite('resources/css/profile/actions.css')
@vite('resources/css/profile/stats.css')
@vite('resources/css/profile/tabs.css')
@vite('resources/css/profile/content.css')
@vite('resources/css/profile/stack.css')
@vite('resources/css/profile/responsive.css')
@vite('resources/css/profile/modalImage.css')
@vite('resources/css/profile/badges.css')
@vite('resources/css/profile/followersModal.css')
@vite('resources/css/profile/projectsModal.css')
@vite('resources/css/profile/githubImport.css')
@endpush

@push('scripts')
@vite('resources/js/projects/modalComment.js')
@vite('resources/js/profile/index.js')
@vite('resources/js/profile/stackModal.js')
@vite('resources/js/profile/badgesModal.js')
@vite('resources/js/profile/followersModal.js')
@vite('resources/js/profile/projectsModal.js')
@vite('resources/js/profile/githubImport.js')
@endpush