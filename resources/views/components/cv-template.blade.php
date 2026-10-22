{{-- resources/views/components/cv-template.blade.php --}}
@props([
'profile',
'user',
'technologies' => collect([]),
'projects' => collect([]),
'workExperiences' => collect([]),
'educations' => collect([]),
'avatarBase64' => null,
'logoBase64' => null,
'qrBase64' => null,
'cvSettings' => [],
])

@php
$urlPerfil = $urlPerfil ?? (request()->getHost() === 'localhost'
    ? 'profile/' . $user->username
    : request()->getHost() . '/profile/' . $user->username);
$cantidadSeguidores = $cantidadSeguidores ?? 0;
$cantidadSiguiendo = $cantidadSiguiendo ?? 0;
$rolProfesional = $rolProfesional ?? 'Software Developer';
$srcAvatar = $avatarBase64 ?? ($profile->avatar ? str_replace('type=normal', 'type=large', $profile->avatar) : '');
$srcLogo = $logoBase64 ?? (function () {
    try {
        return 'data:image/png;base64,'.base64_encode(file_get_contents(public_path('img/logo.png')));
    } catch (\Throwable) {
        return '';
    }
})();
$srcQr = $qrBase64 ?? ($urlQrExterno ?? '');
$cvLocation = trim(($profile->city ?? '') . ', ' . ($profile->country ?? ''), ' ,');
@endphp

<style>
    @page {
        size: A4;
    }

    .cv-doc,
    .cv-doc * {
        box-sizing: border-box;
    }

    .cv-doc {
        --cv-ink: #18181b;
        --cv-body: #3f3f46;
        --cv-muted: #71717a;
        --cv-line: #d4d4d8;

        width: 100%;
        margin: 0 auto;
        background: #ffffff;
        color: var(--cv-ink);
        font-family: 'Segoe UI', -apple-system, BlinkMacSystemFont, 'Liberation Sans', Arial, system-ui, sans-serif;
        font-size: 11px;
        line-height: 1.5;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }

    /* ══ ENCABEZADO ══ */
    .cv-head {
        break-inside: avoid;
        page-break-inside: avoid;
    }

    .cv-head__top {
        display: flex;
        align-items: center;
        gap: 20px;
    }

    .cv-head__avatar {
        width: 84px;
        height: 84px;
        border-radius: 50%;
        object-fit: cover;
        flex-shrink: 0;
    }

    .cv-head__identity {
        flex: 1;
        min-width: 0;
    }

    .cv-head__name {
        margin: 0;
        font-size: 27px;
        font-weight: 700;
        letter-spacing: -.4px;
        line-height: 1.12;
        color: #09090b;
    }

    .cv-head__handle {
        margin-top: 3px;
        font-size: 11.5px;
        color: var(--cv-muted);
    }

    .cv-head__role {
        margin-top: 5px;
        font-size: 12.5px;
        font-weight: 600;
        color: var(--cv-body);
    }

    .cv-head__contact {
        display: flex;
        flex-wrap: wrap;
        gap: 5px 15px;
        margin-top: 15px;
    }

    .cv-contact {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 10.5px;
        color: var(--cv-body);
    }

    .cv-contact svg {
        flex-shrink: 0;
        color: var(--cv-muted);
    }

    .cv-contact--muted {
        color: var(--cv-muted);
    }

    /* ══ CUERPO ══ */
    .cv-body {
        padding-top: 20px;
    }

    .cv-block {
        margin-bottom: 19px;
    }

    .cv-block:last-child {
        margin-bottom: 4px;
    }

    .cv-block__title {
        margin: 0 0 11px;
        padding-bottom: 6px;
        border-bottom: 1px solid var(--cv-line);
        font-size: 10.5px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1.6px;
        color: #09090b;
        break-after: avoid;
        page-break-after: avoid;
    }

    .cv-bio {
        margin: 0;
        font-size: 11.5px;
        line-height: 1.7;
        color: var(--cv-body);
    }

    /* ══ ITEMS ══ */
    .cv-item {
        margin-bottom: 13px;
        break-inside: avoid;
        page-break-inside: avoid;
    }

    .cv-item:last-child {
        margin-bottom: 0;
    }

    .cv-item__row {
        display: flex;
        align-items: baseline;
        justify-content: space-between;
        gap: 12px;
    }

    .cv-item__title {
        margin: 0;
        font-size: 12.5px;
        font-weight: 700;
        color: #09090b;
    }

    .cv-item__date {
        flex-shrink: 0;
        font-size: 10px;
        color: var(--cv-muted);
        white-space: nowrap;
    }

    .cv-item__meta {
        margin-top: 1px;
        font-size: 11.5px;
        color: var(--cv-body);
    }

    .cv-item__meta b {
        font-weight: 600;
        color: var(--cv-ink);
    }

    .cv-item__desc {
        margin: 4px 0 0;
        font-size: 11.5px;
        line-height: 1.6;
        color: var(--cv-body);
    }

    .cv-item__desc strong {
        color: #09090b;
        font-weight: 700;
    }

    .cv-item__tags {
        margin-top: 4px;
        font-size: 10px;
        color: var(--cv-muted);
    }

    /* ══ HABILIDADES ══ */
    .cv-skill {
        display: flex;
        gap: 12px;
        margin-bottom: 6px;
        font-size: 11.5px;
        line-height: 1.6;
        break-inside: avoid;
    }

    .cv-skill:last-child {
        margin-bottom: 0;
    }

    .cv-skill__label {
        flex: 0 0 118px;
        font-size: 9.5px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .6px;
        color: var(--cv-muted);
        padding-top: 1px;
    }

    .cv-skill__value {
        flex: 1;
        min-width: 0;
        display: flex;
        flex-wrap: wrap;
        gap: 4px 16px;
        color: var(--cv-body);
    }

    .cv-tech {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        font-size: 11.5px;
        color: var(--cv-body);
    }

    .cv-tech img {
        width: 15px;
        height: 15px;
        object-fit: contain;
        flex-shrink: 0;
    }

    /* ══ PIE ══ */
    .cv-foot {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
        margin-top: 8px;
        padding-top: 12px;
        border-top: 1px solid var(--cv-line);
        break-inside: avoid;
        page-break-inside: avoid;
    }

    .cv-foot__brand {
        display: flex;
        align-items: center;
        gap: 9px;
        min-width: 0;
    }

    .cv-foot__logo {
        width: 26px;
        height: 26px;
        object-fit: contain;
        flex-shrink: 0;
    }

    .cv-foot__text {
        font-size: 9.5px;
        line-height: 1.5;
        color: var(--cv-muted);
    }

    .cv-foot__qr {
        width: 68px;
        height: 68px;
        flex-shrink: 0;
    }
</style>

<div id="cv-template" class="cv-doc">

    {{-- ══ ENCABEZADO ══ --}}
    <header class="cv-head">
        <div class="cv-head__top">
            @if(($cvSettings['show_photo'] ?? true) && $srcAvatar)
            <img class="cv-head__avatar" src="{{ $srcAvatar }}" alt="{{ $user->name }}" />
            @endif

            <div class="cv-head__identity">
                <h1 class="cv-head__name">{{ $user->name }}</h1>
                <div class="cv-head__handle">&#64;{{ $user->username }}</div>
                <div class="cv-head__role">{{ $rolProfesional }}</div>
            </div>
        </div>

        <div class="cv-head__contact">
            @if(($cvSettings['show_location'] ?? true) && $cvLocation)
            <span class="cv-contact">
                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                {{ $cvLocation }}
            </span>
            @endif

            @if(($cvSettings['show_email'] ?? true) && !empty($user->email))
            <span class="cv-contact">
                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
                {{ $user->email }}
            </span>
            @endif

            @if(!empty($profile->phone_number))
            <span class="cv-contact">
                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                </svg>
                {{ $profile->phone_code ?? '' }} {{ $profile->phone_number }}
            </span>
            @endif

            @if(!empty($profile->website_url))
            <span class="cv-contact">
                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                    <circle cx="12" cy="12" r="10" />
                    <line x1="2" y1="12" x2="22" y2="12" />
                    <path stroke-linecap="round" d="M12 2a15.3 15.3 0 014 10 15.3 15.3 0 01-4 10 15.3 15.3 0 01-4-10 15.3 15.3 0 014-10z" />
                </svg>
                {{ $profile->website_url }}
            </span>
            @endif

            @if(!empty($profile->github_url))
            <span class="cv-contact">
                <svg width="11" height="11" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 0C5.37 0 0 5.37 0 12c0 5.31 3.435 9.795 8.205 11.385.6.105.825-.255.825-.57 0-.285-.015-1.23-.015-2.235-3.015.555-3.795-.735-4.035-1.41-.135-.345-.72-1.41-1.23-1.695-.42-.225-1.02-.78-.015-.795.945-.015 1.62.87 1.845 1.23 1.08 1.815 2.805 1.305 3.495.99.105-.78.42-1.305.765-1.605-2.67-.3-5.46-1.335-5.46-5.925 0-1.305.465-2.385 1.23-3.225-.12-.3-.54-1.53.12-3.18 0 0 1.005-.315 3.3 1.23.96-.27 1.98-.405 3-.405s2.04.135 3 .405c2.295-1.56 3.3-1.23 3.3-1.23.66 1.65.24 2.88.12 3.18.765.84 1.23 1.905 1.23 3.225 0 4.605-2.805 5.625-5.475 5.925.435.375.81 1.095.81 2.22 0 1.605-.015 2.895-.015 3.3 0 .315.225.69.825.57A12.02 12.02 0 0024 12c0-6.63-5.37-12-12-12z" />
                </svg>
                {{ $profile->github_url }}
            </span>
            @endif

            @if(!empty($profile->linkedin_url))
            <span class="cv-contact">
                <svg width="11" height="11" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M4.98 3.5C4.98 4.88 3.87 6 2.5 6S0 4.88 0 3.5 1.12 1 2.5 1s2.48 1.12 2.48 2.5zM.22 8h4.56v14H.22V8zm7.44 0h4.37v1.92h.06c.61-1.15 2.1-2.37 4.32-2.37 4.62 0 5.47 3.04 5.47 6.99V22h-4.56v-6.28c0-1.5-.03-3.42-2.09-3.42-2.09 0-2.41 1.63-2.41 3.31V22H7.66V8z" />
                </svg>
                {{ $profile->linkedin_url }}
            </span>
            @endif

            @if(!empty($profile->twitter_url))
            <span class="cv-contact">
                <svg width="11" height="11" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M18.9 2H22l-6.8 7.8L23 22h-6.3l-4.9-6.4L6.2 22H3l7.3-8.3L2 2h6.4l4.4 5.9L18.9 2zm-1.1 18h1.7L7.3 3.8H5.5L17.8 20z" />
                </svg>
                {{ $profile->twitter_url }}
            </span>
            @endif

            <span class="cv-contact cv-contact--muted">
                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                {{ $cantidadSeguidores }} seguidores · {{ $cantidadSiguiendo }} siguiendo
            </span>

            <span class="cv-contact cv-contact--muted">
                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2" />
                    <line x1="16" y1="2" x2="16" y2="6" />
                    <line x1="8" y1="2" x2="8" y2="6" />
                    <line x1="3" y1="10" x2="21" y2="10" />
                </svg>
                Miembro desde {{ \Carbon\Carbon::parse($profile->created_at ?? now())->translatedFormat('F Y') }}
            </span>
        </div>
    </header>

    {{-- ══ CUERPO ══ --}}
    <main class="cv-body">

        {{-- RESUMEN --}}
        @if(!empty($profile->bio))
        <section class="cv-block">
            <h2 class="cv-block__title">Perfil profesional</h2>
            <p class="cv-bio">{{ $profile->bio }}</p>
        </section>
        @endif

        {{-- SECCIONES DINÁMICAS --}}
        @foreach($cvSettings['section_order'] as $section)
        @switch($section)

        @case('experience')
        @if(($cvSettings['show_experience'] ?? true) && isset($workExperiences) && $workExperiences->isNotEmpty())
        <section class="cv-block">
            <h2 class="cv-block__title">Experiencia Laboral</h2>
            @foreach($workExperiences->take(5) as $experiencia)
            @php
            $fechaInicio = \Carbon\Carbon::parse($experiencia->started_at)->translatedFormat('M Y');
            $fechaFin = $experiencia->current
            ? 'Presente'
            : ($experiencia->ended_at
            ? \Carbon\Carbon::parse($experiencia->ended_at)->translatedFormat('M Y')
            : 'Presente');
            @endphp
            <div class="cv-item">
                <div class="cv-item__row">
                    <h3 class="cv-item__title">{{ $experiencia->position }}</h3>
                    <span class="cv-item__date">{{ $fechaInicio }} — {{ $fechaFin }}</span>
                </div>
                <div class="cv-item__meta">
                    <b>{{ $experiencia->company }}</b>
                    @if(!empty($experiencia->location)) · {{ $experiencia->location }} @endif
                    · {{ \App\Models\WorkExperience::TYPES[$experiencia->type] ?? $experiencia->type }}
                    @if(!empty($experiencia->work_mode))
                    · {{ \App\Models\WorkExperience::WORK_MODES[$experiencia->work_mode] ?? $experiencia->work_mode }}
                    @endif
                    @if(!empty($experiencia->employment_type))
                    · {{ \App\Models\WorkExperience::EMPLOYMENT_TYPES[$experiencia->employment_type] ?? $experiencia->employment_type }}
                    @endif
                </div>
                @if($experiencia->technologies->isNotEmpty())
                <div class="cv-item__meta">
                    Tecnologías: {{ $experiencia->technologies->pluck('name')->implode(', ') }}
                </div>
                @endif
                @if(!empty($experiencia->description))
                <p class="cv-item__desc">{{ $experiencia->description }}</p>
                @endif
            </div>
            @endforeach
        </section>
        @endif
        @break

        @case('projects')
        @if(($cvSettings['show_projects'] ?? true) && $projects->isNotEmpty())
        <section class="cv-block">
            <h2 class="cv-block__title">Proyectos</h2>
            @foreach($projects as $proyecto)
            <div class="cv-item">
                <div class="cv-item__row">
                    <h3 class="cv-item__title">{{ $proyecto->title }}</h3>
                </div>
                @if(!empty($proyecto->content))
                @php
                $techNamesProy = $proyecto->technologies->pluck('name')->sortByDesc(fn($n) => strlen($n))->values();
                $contentProy = e($proyecto->content);
                foreach ($techNamesProy as $tn) {
                $contentProy = preg_replace('/\b('.preg_quote($tn, '/').')\b/i', '<strong>$1</strong>', $contentProy);
                }
                @endphp
                <p class="cv-item__desc">{!! $contentProy !!}</p>
                @endif
                @if($proyecto->technologies->isNotEmpty())
                <p class="cv-item__tags">{{ $proyecto->technologies->pluck('name')->implode(' · ') }}</p>
                @endif
            </div>
            @endforeach
        </section>
        @endif
        @break

        @case('education')
        @if(($cvSettings['show_education'] ?? true) && isset($educations) && $educations->isNotEmpty())
        <section class="cv-block">
            <h2 class="cv-block__title">Educación</h2>
            @foreach($educations->take(5) as $educacion)
            <div class="cv-item">
                <div class="cv-item__row">
                    <h3 class="cv-item__title">{{ $educacion->degree }}</h3>
                    <span class="cv-item__date">
                        @if($educacion->current)
                        En curso
                        @elseif(!empty($educacion->graduated_year))
                        {{ $educacion->graduated_year }}
                        @else
                        —
                        @endif
                    </span>
                </div>
                <div class="cv-item__meta">
                    <b>{{ $educacion->institution }}</b>
                    @if(!empty($educacion->field)) · {{ $educacion->field }} @endif
                </div>
            </div>
            @endforeach
        </section>
        @endif
        @break

        @case('skills')
        @if($technologies->isNotEmpty())
        @php
        $categorias = ['language' => 'Lenguajes', 'framework' => 'Frameworks', 'library' => 'Librerías', 'database' => 'Bases de Datos', 'tool' => 'Herramientas', 'platform' => 'Plataformas'];
        $agrupadas = $technologies->groupBy(fn($t) => $t->category ?? 'tool');
        @endphp
        <section class="cv-block">
            <h2 class="cv-block__title">Habilidades Técnicas</h2>
            @foreach($categorias as $key => $label)
            @if($agrupadas->has($key))
            <div class="cv-skill">
                <span class="cv-skill__label">{{ $label }}</span>
                <span class="cv-skill__value">
                    @foreach($agrupadas[$key] as $tecnologia)
                    <span class="cv-tech">
                        @if($tecnologia->iconoB64 ?? null)
                        <img src="{{ $tecnologia->iconoB64 }}" alt="" />
                        @endif
                        {{ $tecnologia->name }}
                    </span>
                    @endforeach
                </span>
            </div>
            @endif
            @endforeach
        </section>
        @endif
        @break

        @endswitch
        @endforeach

    </main>

    {{-- ══ PIE ══ --}}
    <footer class="cv-foot">
        <div class="cv-foot__brand">
            <img class="cv-foot__logo" src="{{ $srcLogo }}" alt="Fluxa" />
            <span class="cv-foot__text">
                CV generado desde Fluxa, red social para Desarrolladores — {{ $urlPerfil }}
            </span>
        </div>
        <img class="cv-foot__qr" src="{{ $srcQr }}" alt="QR del perfil" />
    </footer>

</div>
