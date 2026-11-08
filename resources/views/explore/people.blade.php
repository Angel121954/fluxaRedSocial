@extends('layouts.app')
@section('title', 'Personas · Fluxa')
@section('meta_description', 'Encuentra desarrolladores latinoamericanos por tecnología, país y especialidad.')
@section('og_title', 'Personas · Fluxa')
@section('og_description', 'Encuentra desarrolladores latinoamericanos por tecnología, país y especialidad.')
@section('og_type', 'website')
@section('canonical', route('explore.people'))

@section('content')
<x-topbar :profile="$profile" />

<div class="feed-layout">
    <div class="feed-main">
        <x-explore-tabs active="people" />

        <div class="people-intro">
            <h1 class="people-title">Personas</h1>
            <p class="people-subtitle">Encuentra desarrolladores por stack, país o especialidad.</p>
        </div>

        <form method="GET" action="{{ route('explore.people') }}" class="people-filters" role="search">
            <div class="people-filter people-filter--search">
                <svg class="people-filter__icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <input type="search"
                    name="q"
                    class="form-input people-filter__input"
                    value="{{ $filters['q'] ?? '' }}"
                    placeholder="Buscar por nombre o @usuario"
                    aria-label="Buscar desarrolladores">
            </div>

            <select name="technology" class="form-input people-filter__select" aria-label="Filtrar por tecnología">
                <option value="">Tecnología</option>
                @foreach($technologies as $technology)
                <option value="{{ $technology->slug }}" {{ ($filters['technology'] ?? '') === $technology->slug ? 'selected' : '' }}>
                    {{ $technology->name }}
                </option>
                @endforeach
            </select>

            <select name="country" class="form-input people-filter__select" aria-label="Filtrar por país">
                <option value="">País</option>
                @foreach($countries as $country)
                <option value="{{ $country }}" {{ ($filters['country'] ?? '') === $country ? 'selected' : '' }}>
                    {{ $country }}
                </option>
                @endforeach
            </select>

            <select name="specialty" class="form-input people-filter__select" aria-label="Filtrar por especialidad">
                <option value="">Especialidad</option>
                @foreach($specialties as $value => $label)
                <option value="{{ $value }}" {{ ($filters['specialty'] ?? '') === $value ? 'selected' : '' }}>
                    {{ $label }}
                </option>
                @endforeach
            </select>

            <select name="sort" class="form-input people-filter__select" aria-label="Ordenar desarrolladores">
                <option value="relevant" {{ ($filters['sort'] ?? 'relevant') === 'relevant' ? 'selected' : '' }}>Recomendados</option>
                <option value="followers" {{ ($filters['sort'] ?? '') === 'followers' ? 'selected' : '' }}>Más seguidos</option>
                <option value="recent" {{ ($filters['sort'] ?? '') === 'recent' ? 'selected' : '' }}>Recientes</option>
            </select>

            <button type="submit" class="people-filter__submit">Filtrar</button>

            @if($hasFilters)
            <a href="{{ route('explore.people') }}" class="people-filter__clear">Limpiar filtros</a>
            @endif
        </form>

        <div class="people-results-head">
            <p class="people-results-count">
                <strong>{{ $people->total() }}</strong> {{ $people->total() === 1 ? 'desarrollador' : 'desarrolladores' }}
            </p>
        </div>

        @if($people->isEmpty())
        <div class="feed-empty">
            <div class="feed-empty-icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                    <circle cx="9" cy="7" r="4" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M22 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75" />
                </svg>
            </div>
            <h3 class="feed-empty-title">
                {{ $hasFilters ? 'No encontramos desarrolladores' : 'Todavía no hay desarrolladores públicos' }}
            </h3>
            <p class="feed-empty-text">
                {{ $hasFilters ? 'Prueba con otra tecnología, país o especialidad.' : 'Cuando los desarrolladores completen su perfil, aparecerán aquí.' }}
            </p>
            @if($hasFilters)
            <a href="{{ route('explore.people') }}" class="feed-empty-cta">Limpiar filtros</a>
            @else
            <a href="{{ route('explore.trending') }}" class="feed-empty-cta">Explorar proyectos</a>
            @endif
        </div>
        @else
        <div class="people-grid">
            @foreach($people as $person)
            @php
                $personProfile = $person->profile;
                $isFollowing = $followingIds->contains($person->id);
            @endphp

            <article class="person-card">
                <div class="person-card__top">
                    <a href="{{ route('profile.show', $person->username) }}" class="person-card__avatar-link">
                        <img src="{{ $person->avatar_url }}"
                            alt="Avatar de {{ $person->name }}"
                            class="person-card__avatar"
                            loading="lazy">
                    </a>

                    <div class="person-card__identity">
                        <a href="{{ route('profile.show', $person->username) }}" class="person-card__name">{{ $person->name }}</a>
                        <span class="person-card__handle">&#64;{{ $person->username }}</span>
                    </div>
                </div>

                @if($personProfile?->specialty)
                <span class="person-card__specialty">
                    {{ \App\Models\Profile::SPECIALTY_LABELS[$personProfile->specialty] ?? ucfirst($personProfile->specialty) }}
                </span>
                @endif

                @if($personProfile?->bio)
                <p class="person-card__bio">{{ Str::limit($personProfile->bio, 130) }}</p>
                @endif

                @if($personProfile?->country)
                <p class="person-card__location">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <span>{{ $personProfile->country }}@if($personProfile->city) · {{ $personProfile->city }}@endif</span>
                </p>
                @endif

                @if($person->technologies->isNotEmpty())
                <div class="person-card__techs">
                    @foreach($person->technologies->take(4) as $technology)
                    <span class="person-card__tech">{{ $technology->name }}</span>
                    @endforeach
                    @if($person->technologies->count() > 4)
                    <span class="person-card__tech person-card__tech--more">+{{ $person->technologies->count() - 4 }}</span>
                    @endif
                </div>
                @endif

                <div class="person-card__footer">
                    <div class="person-card__stats">
                        <span><strong data-followers-count>{{ $person->followers_count }}</strong> {{ $person->followers_count === 1 ? 'seguidor' : 'seguidores' }}</span>
                        <span><strong>{{ $person->public_projects_count }}</strong> {{ $person->public_projects_count === 1 ? 'proyecto' : 'proyectos' }}</span>
                    </div>

                    @if(Auth::user()->role !== 'guest')
                    <button type="button"
                        class="person-card__follow{{ $isFollowing ? ' is-following' : '' }}"
                        data-url="{{ route('users.follow', $person) }}"
                        aria-pressed="{{ $isFollowing ? 'true' : 'false' }}">
                        {{ $isFollowing ? 'Siguiendo' : 'Seguir' }}
                    </button>
                    @else
                    <a href="{{ route('profile.show', $person->username) }}" class="person-card__follow">Ver perfil</a>
                    @endif
                </div>
            </article>
            @endforeach
        </div>

        @if($people->hasPages())
        <nav class="people-pagination" aria-label="Paginación de desarrolladores">
            @if($people->onFirstPage())
            <span class="people-page-btn is-disabled" aria-disabled="true">Anterior</span>
            @else
            <a href="{{ $people->previousPageUrl() }}" class="people-page-btn" rel="prev">Anterior</a>
            @endif

            <span class="people-page-info">Página {{ $people->currentPage() }} de {{ $people->lastPage() }}</span>

            @if($people->hasMorePages())
            <a href="{{ $people->nextPageUrl() }}" class="people-page-btn" rel="next">Siguiente</a>
            @else
            <span class="people-page-btn is-disabled" aria-disabled="true">Siguiente</span>
            @endif
        </nav>
        @endif
        @endif
    </div>
</div>
@endsection

@push('styles')
@vite('resources/css/core/explore.css')
@endpush

@push('scripts')
@vite('resources/js/core/explore/people.js')
@endpush

