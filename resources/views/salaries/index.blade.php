@extends('layouts.app')
@section('title', 'Sueldos · Fluxa')
@section('content')

<x-topbar :profile="$profile ?? null" />

<div class="salary-page">
    <div class="salary-container">

        {{-- Header --}}
        <div class="salary-header">
            <div class="salary-header-text">
                <h1 class="salary-title">Sueldos del developer LATAM</h1>
                <p class="salary-subtitle">Datos anónimos aportados por la comunidad. Conoce rangos salariales por tecnología, país y seniority.</p>
            </div>
            <button class="salary-btn-add" onclick="window.openSalaryModal()">
                <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Aportar mi sueldo
            </button>
        </div>

        {{-- Stats cards --}}
        <div class="salary-stats">
            <div class="salary-stat-card">
                <span class="salary-stat-icon salary-stat-icon--avg">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </span>
                <div class="salary-stat-body">
                    <span class="salary-stat-value">${{ number_format($stats['avg'] ?? 0) }}</span>
                    <span class="salary-stat-label">Promedio LATAM</span>
                </div>
            </div>
            <div class="salary-stat-card">
                <span class="salary-stat-icon salary-stat-icon--count">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </span>
                <div class="salary-stat-body">
                    <span class="salary-stat-value">{{ $stats['count'] ?? 0 }}</span>
                    <span class="salary-stat-label">Reportes</span>
                </div>
            </div>
            <div class="salary-stat-card">
                <span class="salary-stat-icon salary-stat-icon--countries">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </span>
                <div class="salary-stat-body">
                    <span class="salary-stat-value">{{ $stats['byCountry']->count() }}</span>
                    <span class="salary-stat-label">Países</span>
                </div>
            </div>
        </div>

        {{-- Filters --}}
        <div class="salary-filters">
            <div class="salary-filter-group">
                <select class="salary-filter" id="filterCountry">
                    <option value="">Todos los países</option>
                    @foreach($countries as $c)
                    <option value="{{ $c['name'] }}">{{ $c['name'] }}</option>
                    @endforeach
                </select>
                <select class="salary-filter" id="filterSeniority">
                    <option value="">Todos los niveles</option>
                    @foreach($seniorities as $key => $label)
                    <option value="{{ $key }}">{{ $label }}</option>
                    @endforeach
                </select>
                <select class="salary-filter" id="filterModality">
                    <option value="">Todas las modalidades</option>
                    <option value="remote">Remoto</option>
                    <option value="hybrid">Híbrido</option>
                    <option value="onsite">Presencial</option>
                </select>
                <select class="salary-filter" id="filterTechnology">
                    <option value="">Todas las tecnologías</option>
                    @foreach($technologies as $t)
                    <option value="{{ $t->id }}">{{ $t->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        {{-- Table: by technology --}}
        <div class="salary-section">
            <h2 class="salary-section-title">Sueldo promedio por tecnología</h2>
            <div class="salary-table-wrap">
                <table class="salary-table">
                    <thead>
                        <tr>
                            <th>Tecnología</th>
                            <th>Promedio</th>
                            <th>Reportes</th>
                            <th>Junior</th>
                            <th>Senior</th>
                        </tr>
                    </thead>
                    <tbody id="salaryTechTable">
                        @forelse($stats['byTechnology'] as $item)
                        <tr>
                            <td class="salary-tech-cell">
                                @if($item['slug'])
                                <img src="https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/{{ $item['slug'] }}/{{ $item['slug'] }}-original.svg"
                                    alt="{{ $item['technology'] }}" class="salary-tech-icon"
                                    onerror="this.style.display='none'">
                                @endif
                                {{ $item['technology'] }}
                            </td>
                            <td class="salary-avg-cell">${{ number_format($item['avg']) }}</td>
                            <td>{{ $item['count'] }}</td>
                            <td class="salary-muted">—</td>
                            <td class="salary-muted">—</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="salary-empty">Aún no hay suficientes datos. ¡Sé el primero en aportar!</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Recent reports --}}
        <div class="salary-section">
            <h2 class="salary-section-title">Reportes recientes</h2>
            <div class="salary-reports-list" id="salaryReportsList">
                @foreach($stats['reports'] ?? [] as $r)
                <div class="salary-report-item">
                    <div class="salary-report-top">
                        <span class="salary-report-seniority badge-{{ $r->seniority }}">{{ ucfirst($r->seniority) }}</span>
                        <span class="salary-report-amount">${{ number_format($r->salary_usd) }}</span>
                    </div>
                    <div class="salary-report-meta">
                        <span>{{ $r->country }}{{ $r->city ? ' · ' . $r->city : '' }}</span>
                        <span>·</span>
                        <span>{{ $r->experience_years }} años</span>
                        <span>·</span>
                        <span>{{ ucfirst($r->modality) }}</span>
                    </div>
                    <div class="salary-report-techs">
                        @foreach($r->technologies as $tech)
                        <span class="salary-report-tag">{{ $tech->name }}</span>
                        @endforeach
                    </div>
                    <span class="salary-report-date">{{ $r->created_at->diffForHumans() }}</span>
                </div>
                @endforeach
            </div>
        </div>

    </div>
</div>

{{-- Submit modal --}}
@include('salaries.partials.submit-modal')

@endsection

@push('styles')
@vite('resources/css/shared/modal.css')
@vite('resources/css/salaries/index.css')
@endpush

@push('scripts')
@vite('resources/js/salaries/index.js')
@endpush
