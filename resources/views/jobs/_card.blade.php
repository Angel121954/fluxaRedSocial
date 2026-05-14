{{-- ═══════════════════════════════════════════════════════════
     resources/views/jobs/_card.blade.php
     Partial: tarjeta de oferta de empleo
     Variables: $job (App\Models\Job)
══════════════════════════════════════════════════════════ --}}

<article class="job-card {{ $job->is_featured ? 'job-card--featured' : '' }}" data-job-id="{{ $job->id }}">

    <div class="job-card__header">

        {{-- Logo empresa --}}
        <div class="job-card__logo">
            @if ($job->company_logo)
                <img src="{{ $job->company_logo }}" alt="{{ $job->company }}" class="job-card__logo-img">
            @else
                <span class="job-card__logo-placeholder">{{ mb_substr($job->company, 0, 1) }}</span>
            @endif
        </div>

        {{-- Info principal --}}
        <div class="job-card__info">
            <div class="job-card__title-row">
                <div class="job-card__title-wrap">
                    <h3 class="job-card__title">
                        <a href="{{ route('jobs.show', $job->id) }}" class="job-card__title-link">
                            {{ $job->title }}
                        </a>
                    </h3>
                    @if ($job->is_featured)
                        <span class="job-badge job-badge--featured">⭐ Destacado</span>
                    @endif
                    @if ($job->created_at->diffInDays() <= 3)
                        <span class="job-badge job-badge--new">Nuevo</span>
                    @endif
                </div>

                <div class="job-card__actions">
                    <span class="job-card__date" data-timestamp="{{ $job->created_at->timestamp * 1000 }}">
                        Publicado {{ $job->created_at->diffForHumans() }}
                    </span>
                    <button
                        type="button"
                        class="btn-bookmark {{ $job->isSavedBy(auth()->user()) ? 'is-saved' : '' }}"
                        data-job-id="{{ $job->id }}"
                        data-url="{{ route('jobs.bookmark') }}"
                        aria-label="{{ $job->isSavedBy(auth()->user()) ? 'Quitar de guardados' : 'Guardar oferta' }}"
                        aria-pressed="{{ $job->isSavedBy(auth()->user()) ? 'true' : 'false' }}"
                    >
                        {{-- SVG cambia por JS: outline → filled --}}
                        @if ($job->isSavedBy(auth()->user()))
                            <svg fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M5 4a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 20V4z"/></svg>
                        @else
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/></svg>
                        @endif
                    </button>
                </div>
            </div>

            <div class="job-card__meta">
                <span class="job-card__meta-item">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    {{ $job->company }}
                </span>
                <span class="job-card__dot" aria-hidden="true"></span>
                <span class="job-card__meta-item">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a2 2 0 01-2.828 0l-4.243-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    {{ $job->location }}
                </span>
                <span class="job-card__dot" aria-hidden="true"></span>
                <span class="job-card__meta-item">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    {{ $job->modality_label }}
                </span>
                @if ($job->salary_min && $job->salary_max)
                    <span class="job-card__dot" aria-hidden="true"></span>
                    <span class="job-card__salary">
                        ${{ number_format($job->salary_min) }} – ${{ number_format($job->salary_max) }} {{ $job->salary_currency }}/mes
                    </span>
                @endif
            </div>
        </div>
    </div>

    <p class="job-card__desc">{{ Str::limit($job->description, 160) }}</p>

    <div class="job-card__footer">
        <div class="job-card__tags" role="list">
            @foreach ($job->skills->take(5) as $skill)
                <span class="job-tag" role="listitem">{{ $skill->name }}</span>
            @endforeach
        </div>
        <a href="{{ route('jobs.show', $job->id) }}" class="job-card__details-link">
            Ver detalles
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
        </a>
    </div>

</article>
