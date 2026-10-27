{{-- Trayectoria profesional: experiencia, educación y certificaciones. --}}
<div class="career-sections">

    @if($isOwner || $workExperiences->isNotEmpty())
    <section class="career-section">
        <div class="career-section__head">
            <h2 class="career-section__title">Experiencia laboral</h2>
            @if($isOwner)
            <a href="{{ route('work-experiences.index') }}" class="career-section__action">Gestionar</a>
            @endif
        </div>

        @forelse($workExperiences as $experience)
        <article class="career-card">
            <div class="career-card__top">
                <div class="career-card__info">
                    <h3 class="career-card__title">{{ $experience->position }}</h3>
                    <p class="career-card__subtitle">{{ $experience->company }}</p>
                    <p class="career-card__meta">
                        <span class="career-badge career-badge--{{ $experience->type }}">
                            {{ \App\Models\WorkExperience::TYPES[$experience->type] ?? $experience->type }}
                        </span>
                        @if($experience->work_mode)
                        <span class="career-badge">
                            {{ \App\Models\WorkExperience::WORK_MODES[$experience->work_mode] ?? $experience->work_mode }}
                        </span>
                        @endif
                        @if($experience->employment_type)
                        <span>{{ \App\Models\WorkExperience::EMPLOYMENT_TYPES[$experience->employment_type] ?? $experience->employment_type }}</span>
                        @endif
                        @if($experience->location)
                        <span>{{ $experience->location }}</span>
                        @endif
                        <span>
                            {{ $experience->started_at?->translatedFormat('M Y') ?? '' }}
                            &ndash;
                            {{ $experience->current ? 'Actualidad' : ($experience->ended_at?->translatedFormat('M Y') ?? '') }}
                        </span>
                        @if($experience->current)
                        <span class="career-badge career-badge--current">Actual</span>
                        @endif
                    </p>
                </div>
            </div>

            @if($experience->description)
            <p class="career-card__desc">{{ $experience->description }}</p>
            @endif

            @if($experience->technologies->isNotEmpty())
            <div class="career-card__techs">
                @foreach($experience->technologies as $technology)
                <span class="career-chip">{{ $technology->name }}</span>
                @endforeach
            </div>
            @endif
        </article>
        @empty
        <div class="career-empty">
            <p>Aún no has agregado experiencia laboral.</p>
            <a href="{{ route('work-experiences.index') }}" class="career-empty__link">Agregar experiencia</a>
        </div>
        @endforelse
    </section>
    @endif

    @if($isOwner || $educations->isNotEmpty())
    <section class="career-section">
        <div class="career-section__head">
            <h2 class="career-section__title">Educación</h2>
            @if($isOwner)
            <a href="{{ route('educations.index') }}" class="career-section__action">Gestionar</a>
            @endif
        </div>

        @forelse($educations as $education)
        <article class="career-card">
            <div class="career-card__info">
                <h3 class="career-card__title">{{ $education->degree }}</h3>
                <p class="career-card__subtitle">{{ $education->institution }}</p>
                <p class="career-card__meta">
                    @if($education->field)
                    <span>{{ $education->field }}</span>
                    @endif
                    <span>{{ $education->current ? 'En curso' : $education->graduated_year }}</span>
                </p>
            </div>
        </article>
        @empty
        <div class="career-empty">
            <p>Aún no has agregado formación académica.</p>
            <a href="{{ route('educations.index') }}" class="career-empty__link">Agregar educación</a>
        </div>
        @endforelse
    </section>
    @endif

    @if($isOwner || $credentials->isNotEmpty())
    <section class="career-section">
        <div class="career-section__head">
            <h2 class="career-section__title">Certificaciones</h2>
            @if($isOwner)
            <a href="{{ route('credentials.index') }}" class="career-section__action">Gestionar</a>
            @endif
        </div>

        @forelse($credentials as $credential)
        <article class="career-card">
            <div class="career-card__info">
                <h3 class="career-card__title">{{ $credential->title }}</h3>
                <p class="career-card__subtitle">{{ $credential->issuer }}</p>
                <p class="career-card__meta">
                    <span class="career-badge career-badge--{{ $credential->type }}">
                        {{ \App\Models\Credential::TYPES[$credential->type] ?? $credential->type }}
                    </span>
                    <span>Expedida {{ $credential->issued_at?->translatedFormat('M Y') ?? '' }}</span>
                    @if($credential->expires_at)
                    <span>Válida hasta {{ $credential->expires_at->translatedFormat('M Y') }}</span>
                    @endif
                    @if($isOwner)
                    <span class="career-badge career-badge--{{ $credential->privacy }}">
                        {{ \App\Models\Credential::PRIVACY[$credential->privacy] ?? $credential->privacy }}
                    </span>
                    @endif
                </p>
            </div>

            @if($credential->description)
            <p class="career-card__desc">{{ $credential->description }}</p>
            @endif

            @if($credential->credential_id)
            <p class="career-card__detail"><strong>ID:</strong> {{ $credential->credential_id }}</p>
            @endif

            @if($credential->credential_url)
            <a href="{{ $credential->credential_url }}" class="career-card__link" target="_blank" rel="noopener noreferrer">
                Ver credencial
            </a>
            @endif
        </article>
        @empty
        <div class="career-empty">
            <p>Aún no has agregado certificaciones.</p>
            <a href="{{ route('credentials.index') }}" class="career-empty__link">Agregar certificación</a>
        </div>
        @endforelse
    </section>
    @endif

</div>
