{{-- ═══════════════════════════════════════════════════════════
     resources/views/jobs/index.blade.php
     Vista principal: Bolsa de empleo
     Vite entry: resources/js/jobs/index.js
     CSS entry:  resources/css/jobs.css
══════════════════════════════════════════════════════════ --}}

@extends('layouts.app')

@section('title', 'Bolsa de empleo · Fluxa')

@push('styles')
    @vite('resources/css/jobs/jobs.css')
@endpush

@section('content')

<x-topbar :profile="$profile ?? null" />

<div class="jobs-page">

    {{-- ── Encabezado ────────────────────────────────────────────────── --}}
    <div class="jobs-header">
        <h1 class="jobs-title">Bolsa de empleo</h1>
        <p class="jobs-subtitle">Encuentra oportunidades que impulsen tu carrera profesional.</p>
    </div>

    <div class="jobs-layout">

        {{-- ════════════════════════════════════════════════════════════
             COLUMNA PRINCIPAL
        ════════════════════════════════════════════════════════════ --}}
        <div class="jobs-main">

            {{-- ── Buscador + filtros ──────────────────────────────── --}}
            <div class="jobs-search-box">
                <form
                    id="jobsSearchForm"
                    action="{{ route('jobs.index') }}"
                    method="GET"
                    class="jobs-search-row"
                >
                    {{-- Keyword --}}
                    <div class="jobs-input-wrap">
                        <svg class="jobs-input-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z"/>
                        </svg>
                        <input
                            id="jobKeyword"
                            type="text"
                            name="q"
                            value="{{ request('q') }}"
                            placeholder="Buscar empleos por título, empresa o palabra clave"
                            class="jobs-input"
                            autocomplete="off"
                        >
                    </div>

                    {{-- Ubicación --}}
                    <div class="jobs-input-wrap">
                        <svg class="jobs-input-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <input
                            id="jobLocation"
                            type="text"
                            name="location"
                            value="{{ request('location') }}"
                            placeholder="Ubicación"
                            class="jobs-input"
                            autocomplete="off"
                        >
                    </div>

                    {{-- Modalidad --}}
                    <select name="modality" class="jobs-select">
                        <option value="">Modalidad</option>
                        <option value="remoto"    {{ request('modality') === 'remoto'    ? 'selected' : '' }}>Remoto</option>
                        <option value="hibrido"   {{ request('modality') === 'hibrido'   ? 'selected' : '' }}>Híbrido</option>
                        <option value="presencial"{{ request('modality') === 'presencial' ? 'selected' : '' }}>Presencial</option>
                    </select>

                    <button type="submit" class="btn-jobs-search">Buscar</button>
                </form>

                {{-- Filtros rápidos --}}
                <div class="jobs-quick-filters" id="jobQuickFilters">
                    <span class="jobs-filter-label">Filtros rápidos:</span>
                    @foreach([
                        'remoto'          => 'Remoto',
                        'tiempo-completo' => 'Tiempo completo',
                        'medio-tiempo'    => 'Medio tiempo',
                        'practicas'       => 'Prácticas',
                        'freelance'       => 'Freelance',
                        'tecnologia'      => 'Tecnología',
                    ] as $value => $label)
                        <button
                            type="button"
                            class="jobs-pill {{ in_array($value, (array) request('tags', [])) ? 'is-active' : '' }}"
                            data-filter="{{ $value }}"
                        >{{ $label }}</button>
                    @endforeach

                    <button type="button" class="jobs-pill jobs-pill--more" id="btnMoreFilters">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"
                             style="width:14px;height:14px;flex-shrink:0">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/>
                        </svg>
                        Más filtros
                    </button>
                </div>
            </div>

            {{-- ── Header resultados ───────────────────────────────── --}}
            <div class="jobs-results-header">
                <div class="jobs-results-title">
                    <svg fill="currentColor" viewBox="0 0 20 20" class="jobs-star-icon" aria-hidden="true">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                    </svg>
                    Empleos destacados
                    <span class="jobs-results-count">{{ $jobs->total() }} resultados</span>
                </div>
                <div class="jobs-sort-wrap">
                    <label for="jobsSort" class="jobs-sort-label">Ordenar por:</label>
                    <select id="jobsSort" name="sort" class="jobs-select jobs-select--sm" form="jobsSearchForm">
                        <option value="recent"   {{ request('sort', 'recent') === 'recent'   ? 'selected' : '' }}>Más recientes</option>
                        <option value="relevant" {{ request('sort') === 'relevant'            ? 'selected' : '' }}>Más relevantes</option>
                        <option value="salary"   {{ request('sort') === 'salary'              ? 'selected' : '' }}>Mayor salario</option>
                    </select>
                </div>
            </div>

            {{-- ── Lista de ofertas ─────────────────────────────────── --}}
            <div class="jobs-list" id="jobsList">
                @forelse ($jobs as $job)
                    @include('jobs._card', ['job' => $job])
                @empty
                    <div class="jobs-empty">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="jobs-empty-icon" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                  d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        <h3 class="jobs-empty-title">No encontramos ofertas</h3>
                        <p class="jobs-empty-text">Intenta con otros filtros o amplía tu búsqueda.</p>
                    </div>
                @endforelse
            </div>

            {{-- Paginación / cargar más --}}
            @if ($jobs->hasMorePages())
                <div class="jobs-load-more-wrap" id="loadMoreWrap">
                    <button
                        type="button"
                        class="btn-load-more"
                        id="btnLoadMore"
                        data-url="{{ $jobs->nextPageUrl() }}"
                    >
                        Ver más ofertas
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"
                             style="width:15px;height:15px">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                </div>
            @endif

        </div>{{-- /jobs-main --}}

    </div>{{-- /jobs-layout --}}

</div>{{-- /jobs-page --}}

@endsection

@push('scripts')
    @vite('resources/js/jobs/index.js')
@endpush
