@extends('admin.layouts.admin')

@section('title', 'Reportes — Admin Fluxa')

@push('styles')
@vite('resources/css/shared/modal.css')
@vite('resources/css/admin/users.css')
@vite('resources/css/admin/reports.css')
@endpush

@section('admin-content')

<div class="page-header">
    <div>
        <h1 class="page-title">Reportes y mensajes</h1>
        <p class="page-sub">Revisa y gestiona los reportes de la comunidad y los mensajes de contacto.</p>
    </div>
</div>

{{-- ─── Quick stats ──────────────────────────────────────────── --}}
<div class="adm-quick-stats rp-quick-stats">
    <div class="adm-qs-card">
        <span class="adm-qs-label">Reportes de usuarios</span>
        <span class="adm-qs-value adm-qs-value--red">{{ $counts['user'] }}</span>
    </div>
    <div class="adm-qs-card">
        <span class="adm-qs-label">Reportes de proyectos</span>
        <span class="adm-qs-value adm-qs-value--red">{{ $counts['project'] }}</span>
    </div>
    <div class="adm-qs-card">
        <span class="adm-qs-label">Reportes del diario</span>
        <span class="adm-qs-value adm-qs-value--red">{{ $counts['diary'] }}</span>
    </div>
    <div class="adm-qs-card">
        <span class="adm-qs-label">Problemas reportados</span>
        <span class="adm-qs-value adm-qs-value--blue">{{ $counts['problem'] }}</span>
    </div>
    <div class="adm-qs-card">
        <span class="adm-qs-label">Mensajes contacto</span>
        <span class="adm-qs-value adm-qs-value--accent">{{ $counts['contact'] }}
            @if($counts['unread_contacts'] > 0)
            <span class="rp-unread-badge">{{ $counts['unread_contacts'] }} sin leer</span>
            @endif
        </span>
    </div>
</div>

{{-- ─── Tabs ─────────────────────────────────────────────────── --}}
<div class="rp-tabs" role="tablist">
    <button class="rp-tab rp-tab--active" role="tab" aria-selected="true" data-tab="user-reports">
        <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2" />
            <circle cx="9" cy="7" r="4" />
        </svg>
        Usuarios
        @if($counts['user'] > 0)<span class="rp-tab-count">{{ $counts['user'] }}</span>@endif
    </button>
    <button class="rp-tab" role="tab" aria-selected="false" data-tab="project-reports">
        <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
            <rect x="3" y="3" width="18" height="18" rx="2" ry="2" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
            <line x1="3" y1="9" x2="21" y2="9" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
        </svg>
        Proyectos
        @if($counts['project'] > 0)<span class="rp-tab-count">{{ $counts['project'] }}</span>@endif
    </button>
    <button class="rp-tab" role="tab" aria-selected="false" data-tab="diary-reports">
        <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
            <rect x="3" y="4" width="18" height="18" rx="2" ry="2" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
            <line x1="16" y1="2" x2="16" y2="6" />
            <line x1="8" y1="2" x2="8" y2="6" />
            <line x1="3" y1="10" x2="21" y2="10" />
        </svg>
        Diario
        @if($counts['diary'] > 0)<span class="rp-tab-count">{{ $counts['diary'] }}</span>@endif
    </button>
    <button class="rp-tab" role="tab" aria-selected="false" data-tab="problem-reports">
        <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
        </svg>
        Problemas
        @if($counts['problem'] > 0)<span class="rp-tab-count">{{ $counts['problem'] }}</span>@endif
    </button>
    <button class="rp-tab" role="tab" aria-selected="false" data-tab="contacts">
        <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
        </svg>
        Contacto
        @if($counts['unread_contacts'] > 0)<span class="rp-tab-count rp-tab-count--amber">{{ $counts['unread_contacts'] }}</span>@endif
    </button>
</div>

{{-- ═══════════════════════════════════════════════════════════════
     TAB 1 — Reportes de usuarios
     ═══════════════════════════════════════════════════════════════ --}}
<div class="rp-panel rp-panel--active" id="tab-user-reports" role="tabpanel">
    @include('admin.reports.partials.user-reports')
</div>

{{-- ═══════════════════════════════════════════════════════════════
     TAB 2 — Reportes de proyectos
     ═══════════════════════════════════════════════════════════════ --}}
<div class="rp-panel" id="tab-project-reports" role="tabpanel">
    @include('admin.reports.partials.project-reports')
</div>

{{-- ═══════════════════════════════════════════════════════════════
     TAB 3 — Reportes del diario
     ═══════════════════════════════════════════════════════════════ --}}
<div class="rp-panel" id="tab-diary-reports" role="tabpanel">
    @include('admin.reports.partials.diary-reports')
</div>

{{-- ═══════════════════════════════════════════════════════════════
     TAB 4 — Problemas reportados
     ═══════════════════════════════════════════════════════════════ --}}
<div class="rp-panel" id="tab-problem-reports" role="tabpanel">
    @include('admin.reports.partials.problem-reports')
</div>

{{-- ═══════════════════════════════════════════════════════════════
     TAB 5 — Mensajes de contacto
     ═══════════════════════════════════════════════════════════════ --}}
<div class="rp-panel" id="tab-contacts" role="tabpanel">
    @include('admin.reports.partials.contacts')
</div>

@endsection

@push('scripts')
@vite('resources/js/admin/reports/index.js')
@endpush
