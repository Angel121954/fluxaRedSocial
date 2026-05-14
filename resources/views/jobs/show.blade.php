@extends('layouts.app')

@section('title', $job->title . ' · Fluxa')

@push('styles')
    @vite('resources/css/jobs/jobs.css')
@endpush

@section('content')

<x-topbar :profile="$profile ?? null" />

<div class="jobs-page">

    <article class="job-detail">
        <a href="{{ route('jobs.index') }}" class="job-back-link">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:16px;height:16px">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 12H5m7-7l-7 7 7 7"/>
            </svg>
            Volver a ofertas
        </a>

        <div class="job-detail__header">
            <div class="job-detail__logo">
                @if ($job->company_logo)
                    <img src="{{ $job->company_logo }}" alt="{{ $job->company }}" class="job-detail__logo-img">
                @else
                    <span class="job-detail__logo-placeholder">{{ mb_substr($job->company, 0, 1) }}</span>
                @endif
            </div>
            <div class="job-detail__info">
                <h1 class="job-detail__title">{{ $job->title }}</h1>
                <p class="job-detail__company">{{ $job->company }}</p>
                <div class="job-detail__meta">
                    <span>{{ $job->location }}</span>
                    <span class="job-card__dot" aria-hidden="true"></span>
                    <span>{{ $job->modality_label }}</span>
                    @if ($job->salary_min && $job->salary_max)
                        <span class="job-card__dot" aria-hidden="true"></span>
                        <span class="job-detail__salary">${{ number_format($job->salary_min) }} – ${{ number_format($job->salary_max) }} {{ $job->salary_currency }}/mes</span>
                    @endif
                </div>
            </div>
        </div>

        <div class="job-detail__body">
            <h2>Descripción</h2>
            <p>{{ $job->description }}</p>
        </div>

        @if ($job->skills->isNotEmpty())
            <div class="job-detail__skills">
                <h2>Habilidades requeridas</h2>
                <div class="job-card__tags">
                    @foreach ($job->skills as $skill)
                        <span class="job-tag">{{ $skill->name }}</span>
                    @endforeach
                </div>
            </div>
        @endif

        <div class="job-detail__actions">
            <button type="button"
                    class="btn-bookmark {{ $job->isSavedBy(auth()->user()) ? 'is-saved' : '' }}"
                    data-job-id="{{ $job->id }}"
                    data-url="{{ route('jobs.bookmark') }}">
                @if ($job->isSavedBy(auth()->user()))
                    <svg fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M5 4a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 20V4z"/></svg>
                @else
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/></svg>
                @endif
                {{ $job->isSavedBy(auth()->user()) ? 'Guardado' : 'Guardar' }}
            </button>
        </div>
    </article>
</div>
@endsection

@push('scripts')
    @vite('resources/js/jobs/index.js')
@endpush
