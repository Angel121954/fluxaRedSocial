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

        @if ($job->requirements)
            <div class="job-detail__body">
                <h2>Requisitos</h2>
                <p>{{ $job->requirements }}</p>
            </div>
        @endif

        @if ($job->benefits)
            <div class="job-detail__body">
                <h2>Beneficios</h2>
                <p>{{ $job->benefits }}</p>
            </div>
        @endif

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

            @if ($job->whatsapp)
            <a href="https://wa.me/{{ $job->whatsapp }}" target="_blank" rel="noopener noreferrer"
               class="btn-whatsapp" aria-label="Contactar por WhatsApp">
                <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                </svg>
            </a>
            @endif

            @if ($job->application_url)
            <a href="{{ $job->application_url }}" target="_blank" rel="noopener noreferrer"
               class="btn-apply" aria-label="Postularse">
                Postularse
            </a>
            @endif
        </div>
    </article>
</div>
@endsection

@push('scripts')
    @vite('resources/js/jobs/index.js')
@endpush
