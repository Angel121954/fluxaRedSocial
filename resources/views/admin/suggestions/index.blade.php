@extends('layouts.app')

@section('title', 'Sugerencias — Admin Fluxa')

@push('styles')
@vite('resources/css/admin/suggestions.css')
@endpush

@section('content')

{{-- ─── Topbar ─────────────────────────────── --}}
<x-topbar :profile="$profile" />

{{-- ─── Page shell ─────────────────────────── --}}
<div class="adm-wrap">

    {{-- Breadcrumb --}}
    <nav class="adm-breadcrumb" aria-label="Breadcrumb">
        <a href="{{ route('explore.index') }}" class="adm-bc-link">
            <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
            </svg>
            Inicio
        </a>
        <span class="adm-bc-sep" aria-hidden="true">/</span>
        <a href="#" class="adm-bc-link">Admin</a>
        <span class="adm-bc-sep" aria-hidden="true">/</span>
        <span class="adm-bc-current" aria-current="page">Sugerencias</span>
    </nav>

    {{-- Page header --}}
    <header class="adm-header">
        <div>
            <h1 class="adm-title">Sugerencias</h1>
            <p class="adm-subtitle">Gestiona y revisa las sugerencias enviadas por los usuarios.</p>
        </div>
    </header>

    {{-- ─── Card principal ─── --}}
    <div class="adm-card">

        {{-- ── Toolbar (filtros via GET) ── --}}
        <form method="GET" action="{{ route('admin.suggestions.index') }}" id="filterForm">
            <div class="adm-toolbar">

                {{-- Search --}}
                <div class="adm-search-wrap">
                    <svg class="adm-search-icon" width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <input
                        type="search"
                        name="search"
                        class="adm-search"
                        placeholder="Buscar..."
                        value="{{ request('search') }}"
                        aria-label="Buscar sugerencias">
                </div>

                {{-- Filters group --}}
                <div class="adm-filters">

                    {{-- Estado --}}
                    <div class="adm-select-wrap">
                        <select name="status" class="adm-select" aria-label="Filtrar por estado"
                            onchange="document.getElementById('filterForm').submit()">
                            <option value="">Todas las sugerencias</option>
                            <option value="pending" {{ request('status') === 'pending'   ? 'selected' : '' }}>Pendiente</option>
                            <option value="approved" {{ request('status') === 'approved'  ? 'selected' : '' }}>Aprobado</option>
                            <option value="reviewing" {{ request('status') === 'reviewing' ? 'selected' : '' }}>En revisión</option>
                            <option value="rejected" {{ request('status') === 'rejected'  ? 'selected' : '' }}>Rechazado</option>
                        </select>
                        <svg class="adm-select-chevron" width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7" />
                        </svg>
                    </div>

                    {{-- Fechas --}}
                    <div class="adm-select-wrap">
                        <select name="date" class="adm-select" aria-label="Filtrar por fecha"
                            onchange="document.getElementById('filterForm').submit()">
                            <option value="">Todas las fechas</option>
                            <option value="today" {{ request('date') === 'today' ? 'selected' : '' }}>Hoy</option>
                            <option value="week" {{ request('date') === 'week'  ? 'selected' : '' }}>Esta semana</option>
                            <option value="month" {{ request('date') === 'month' ? 'selected' : '' }}>Este mes</option>
                        </select>
                        <svg class="adm-select-chevron" width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7" />
                        </svg>
                    </div>

                    {{-- Ordenar --}}
                    <div class="adm-select-wrap">
                        <select name="order" class="adm-select" aria-label="Ordenar por"
                            onchange="document.getElementById('filterForm').submit()">
                            <option value="latest" {{ request('order', 'latest') === 'latest' ? 'selected' : '' }}>Ordenar por: Más recientes</option>
                            <option value="oldest" {{ request('order') === 'oldest' ? 'selected' : '' }}>Más antiguos</option>
                        </select>
                        <svg class="adm-select-chevron" width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7" />
                        </svg>
                    </div>

                    {{-- Botones --}}
                    <div class="adm-btn-group">
                        <button type="submit" class="adm-btn-icon adm-btn-accent" title="Aplicar filtros" aria-label="Aplicar filtros">
                            <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z" />
                            </svg>
                        </button>
                        <a href="{{ route('admin.suggestions.index') }}" class="adm-btn-icon" title="Limpiar filtros" aria-label="Limpiar filtros">
                            <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </a>
                    </div>

                </div>
            </div>
        </form>

        {{-- ─── Table ─── --}}
        <div class="adm-table-wrap">
            <table class="adm-table" role="table" aria-label="Tabla de sugerencias">
                <thead>
                    <tr>
                        <th class="adm-th" scope="col">Estado</th>
                        <th class="adm-th" scope="col">Usuario</th>
                        <th class="adm-th adm-th--desc" scope="col">Descripción</th>
                        <th class="adm-th adm-th--center" scope="col">Imagen</th>
                        <th class="adm-th adm-th--right" scope="col">Fecha</th>
                        <th class="adm-th adm-th--center" scope="col">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($suggestions as $suggestion)
                    <tr class="adm-tr">

                        {{-- Estado --}}
                        <td class="adm-td">
                            <span class="adm-badge adm-badge--{{ $suggestion->status }}">
                                <span class="adm-badge-dot"></span>
                                @switch($suggestion->status)
                                @case('pending') Pendiente @break
                                @case('approved') Aprobado @break
                                @case('reviewing') En revisión @break
                                @case('rejected') Rechazado @break
                                @default {{ ucfirst($suggestion->status) }}
                                @endswitch
                            </span>
                        </td>

                        {{-- Usuario --}}
                        <td class="adm-td">
                            <div class="adm-user">
                                <div class="adm-user-info">
                                    <span class="adm-user-name">{{ $suggestion->user->name }}</span>
                                    <span class="adm-user-handle">{{ '@' . $suggestion->user->username }}</span>
                                </div>
                            </div>
                        </td>

                        {{-- Descripción --}}
                        <td class="adm-td adm-td--desc">
                            <span class="adm-desc">{{ $suggestion->description }}</span>
                        </td>

                        {{-- Imagen --}}
                        <td class="adm-td adm-td--center">
                            @if($suggestion->image_url)
                            <div class="adm-thumb-wrap">
                                <img src="{{ $suggestion->image_url }}"
                                    alt="Imagen adjunta"
                                    class="adm-thumb"
                                    loading="lazy">
                            </div>
                            @else
                            <span class="adm-empty-cell" aria-label="Sin imagen">—</span>
                            @endif
                        </td>

                        {{-- Fecha --}}
                        <td class="adm-td adm-td--right">
                            <time class="adm-date" datetime="{{ $suggestion->created_at->toDateString() }}">
                                {{ $suggestion->created_at->translatedFormat('d M Y') }}
                            </time>
                        </td>

                        {{-- Acciones --}}
                        <td class="adm-td adm-td--center">
                            <div class="adm-actions-wrap">
                                <button
                                    type="button"
                                    class="adm-actions-btn"
                                    data-dropdown-target="dropdown-{{ $suggestion->id }}"
                                    aria-label="Acciones para esta sugerencia"
                                    aria-expanded="false">
                                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" />
                                    </svg>
                                </button>

                                <div class="adm-dropdown" id="dropdown-{{ $suggestion->id }}" role="menu" aria-label="Opciones">

                                    {{-- Aprobar --}}
                                    <form method="POST" action="{{ route('admin.suggestions.approve', $suggestion) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="adm-dropdown-item" role="menuitem">
                                            <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                            </svg>
                                            Aprobar
                                        </button>
                                    </form>

                                    {{-- Marcar como leído --}}
                                    <form method="POST" action="{{ route('admin.suggestions.markRead', $suggestion) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="adm-dropdown-item" role="menuitem">
                                            <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                            </svg>
                                            Marcar como leído
                                        </button>
                                    </form>

                                    <div class="adm-dropdown-divider" role="separator"></div>

                                    {{-- Eliminar --}}
                                    <form method="POST" action="{{ route('admin.suggestions.destroy', $suggestion) }}" class="form-delete-suggestion">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="adm-dropdown-item adm-dropdown-item--danger" role="menuitem">
                                            <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                            Eliminar
                                        </button>
                                    </form>

                                </div>
                            </div>
                        </td>

                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="adm-empty">
                            <div class="adm-empty-state">
                                <svg width="40" height="40" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <p>No se encontraron sugerencias</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- ─── Pagination ─── --}}
        @if($suggestions->hasPages())
        <div class="adm-pagination">

            {{-- Prev --}}
            @if($suggestions->onFirstPage())
            <span class="adm-page-btn adm-page-btn--disabled" aria-disabled="true" aria-label="Página anterior">
                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </span>
            @else
            <a href="{{ $suggestions->previousPageUrl() }}" class="adm-page-btn" aria-label="Página anterior">
                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </a>
            @endif

            {{-- Números de página --}}
            @foreach($suggestions->getUrlRange(max(1, $suggestions->currentPage() - 2), min($suggestions->lastPage(), $suggestions->currentPage() + 2)) as $page => $url)
            @if($page == $suggestions->currentPage())
            <span class="adm-page-btn adm-page-btn--active" aria-current="page">{{ $page }}</span>
            @else
            <a href="{{ $url }}" class="adm-page-btn" aria-label="Ir a página {{ $page }}">{{ $page }}</a>
            @endif
            @endforeach

            {{-- Next --}}
            @if($suggestions->hasMorePages())
            <a href="{{ $suggestions->nextPageUrl() }}" class="adm-page-btn" aria-label="Página siguiente">
                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </a>
            @else
            <span class="adm-page-btn adm-page-btn--disabled" aria-disabled="true" aria-label="Página siguiente">
                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </span>
            @endif

        </div>
        @endif

    </div>{{-- /adm-card --}}
</div>{{-- /adm-wrap --}}

@push('scripts')
@vite('resources/js/onboarding/index.js')
@endpush

@endsection