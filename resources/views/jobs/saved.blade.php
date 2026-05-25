@extends('layouts.app')

@section('title', 'Ofertas guardadas · Fluxa')

@push('styles')
@vite('resources/css/jobs/jobs.css')
@endpush

@section('content')

<x-topbar :profile="$profile ?? null" />

<div class="jobs-page">
    <div class="jobs-header">
        <h1 class="jobs-title">Ofertas guardadas</h1>
        <p class="jobs-subtitle">Tus ofertas de empleo favoritas, todas en un solo lugar.</p>
    </div>

    <div class="jobs-list">
        @forelse ($jobs as $job)
        @include('jobs._card', ['job' => $job])
        @empty
        <div class="jobs-empty">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="jobs-empty-icon" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                    d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" />
            </svg>
            <h3 class="jobs-empty-title">No tienes ofertas guardadas</h3>
            <p class="jobs-empty-text">Explora ofertas y guarda las que te interesen.</p>
            <a href="{{ route('jobs.index') }}" class="btn-jobs-publish" style="margin-top:1rem">Explorar ofertas</a>
        </div>
        @endforelse
    </div>

    @if ($jobs->hasPages())
    <div class="jobs-pagination">
        {{ $jobs->links() }}
    </div>
    @endif
</div>
@endsection