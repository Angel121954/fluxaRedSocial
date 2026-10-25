@extends('admin.layouts.admin')

@section('title', 'Panel Administrativo - Fluxa')

@section('admin-content')
<div class="page-header">
    <div>
        <h1 class="page-title">Panel Administrativo</h1>
        <p class="page-sub">Bienvenido de nuevo, {{ auth()->user()->name }}. Aquí tienes un resumen general de Fluxa.</p>
    </div>

    <details class="date-range">
        <summary class="date-btn" aria-label="Filtrar por rango de fechas">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <rect x="3" y="4" width="18" height="18" rx="2" />
                <line x1="16" y1="2" x2="16" y2="6" />
                <line x1="8" y1="2" x2="8" y2="6" />
                <line x1="3" y1="10" x2="21" y2="10" />
            </svg>
            <span>{{ $etiquetaRango }}</span>
        </summary>

        <form class="date-popover" method="GET" action="{{ route('admin.dashboard') }}">
            <div class="date-field">
                <label for="dateFrom">Desde</label>
                <input type="date" id="dateFrom" name="desde" value="{{ $desde?->format('Y-m-d') }}" max="{{ now()->format('Y-m-d') }}">
            </div>
            <div class="date-field">
                <label for="dateTo">Hasta</label>
                <input type="date" id="dateTo" name="hasta" value="{{ $hasta?->format('Y-m-d') }}" max="{{ now()->format('Y-m-d') }}">
            </div>
            <div class="date-actions">
                <a class="date-clear" href="{{ route('admin.dashboard') }}">Limpiar</a>
                <button type="submit" class="date-apply">Aplicar</button>
            </div>
        </form>
    </details>
</div>

@include('admin.partials.stats-cards')

<div class="content-grid">
    <div class="col-left">
        @include('admin.partials.growth-chart')
        @include('admin.partials.donut-charts')
    </div>
    <div class="col-right">
        @include('admin.partials.activity')
        @include('admin.partials.top-users')
    </div>
</div>
@endsection
