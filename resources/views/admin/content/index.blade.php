@extends('admin.layouts.admin')

@section('title', 'Contenido — Admin Fluxa')

@push('styles')
@vite('resources/css/admin/users.css')
@vite('resources/css/admin/content.css')
@endpush

@section('admin-content')

<div class="page-header">
    <div>
        <h1 class="page-title">Gestión de contenido</h1>
        <p class="page-sub">Administra los proyectos y comentarios de la plataforma.</p>
    </div>
</div>

{{-- ─── Quick stats ──────────────────────────────────────────── --}}
<div class="adm-quick-stats ct-quick-stats">
    <div class="adm-qs-card">
        <span class="adm-qs-label">Proyectos activos</span>
        <span class="adm-qs-value adm-qs-value--accent">{{ $counts['projects'] }}</span>
    </div>
    <div class="adm-qs-card">
        <span class="adm-qs-label">Proyectos eliminados</span>
        <span class="adm-qs-value adm-qs-value--red">{{ $counts['projects_trashed'] }}</span>
    </div>
    <div class="adm-qs-card">
        <span class="adm-qs-label">Comentarios</span>
        <span class="adm-qs-value adm-qs-value--blue">{{ $counts['comments'] }}</span>
    </div>
</div>

{{-- ─── Tabs ─────────────────────────────────────────────────── --}}
<div class="ct-tabs" role="tablist">
    <button class="ct-tab ct-tab--active" role="tab" aria-selected="true" data-tab="projects">
        <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
            <rect x="3" y="3" width="18" height="18" rx="2" ry="2" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
            <line x1="3" y1="9" x2="21" y2="9" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
        </svg>
        Proyectos
        @if($counts['projects'] > 0)<span class="ct-tab-count">{{ $counts['projects'] }}</span>@endif
    </button>
    <button class="ct-tab" role="tab" aria-selected="false" data-tab="comments">
        <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z" />
        </svg>
        Comentarios
        @if($counts['comments'] > 0)<span class="ct-tab-count">{{ $counts['comments'] }}</span>@endif
    </button>
</div>

{{-- ═══════════════════════════════════════════════════════════════
     TAB 1 — Proyectos
     ═══════════════════════════════════════════════════════════════ --}}
<div class="ct-panel ct-panel--active" id="tab-projects" role="tabpanel">
    @include('admin.content.partials.projects-tab')
</div>

{{-- ═══════════════════════════════════════════════════════════════
     TAB 2 — Comentarios
     ═══════════════════════════════════════════════════════════════ --}}
<div class="ct-panel" id="tab-comments" role="tabpanel">
    @include('admin.content.partials.comments-tab')
</div>

@endsection

@push('scripts')
@vite('resources/js/admin/content/index.js')
@endpush
