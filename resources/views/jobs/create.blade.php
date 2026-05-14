@extends('layouts.app')

@section('title', 'Publicar oferta · Fluxa')

@push('styles')
    @vite('resources/css/jobs/jobs.css')
@endpush

@section('content')

<x-topbar :profile="$profile ?? null" />

<div class="jobs-page">
    <div class="jobs-header">
        <h1 class="jobs-title">Publicar oferta de empleo</h1>
        <p class="jobs-subtitle">Próximamente podrás publicar ofertas para llegar a miles de desarrolladores.</p>
    </div>

    <div class="jobs-empty" style="padding:4rem 1rem">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="jobs-empty-icon" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                  d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
        </svg>
        <h3 class="jobs-empty-title">Esta funcionalidad está en desarrollo</h3>
        <p class="jobs-empty-text">Muy pronto podrás publicar tus ofertas de empleo aquí.</p>
        <a href="{{ route('jobs.index') }}" class="btn-jobs-publish" style="margin-top:1rem">Ver ofertas</a>
    </div>
</div>
@endsection
