@extends('layouts.app')

@section('title', $project->title.' · '.($project->user?->name ?? 'Fluxa'))
@section('meta_description', Str::limit(strip_tags($project->content), 155))
@section('og_title', $project->title)
@section('og_description', Str::limit(strip_tags($project->content), 155))
@section('og_type', 'article')
@section('og_image', $project->cover_url ?? asset('img/favicon.png'))
@if($project->cover_url)
@section('og_image_width', '1200')
@section('og_image_height', '630')
@endif
@section('og_url', route('projects.show', $project))
@section('canonical', route('projects.show', $project))

@push('meta')
<script type="application/ld+json">
    {!! json_encode([
        '@context' => 'https://schema.org',
        '@type' => 'CreativeWork',
        'name' => $project->title,
        'description' => Str::limit(strip_tags($project->content), 200),
        'datePublished' => $project->created_at?->toAtomString(),
        'dateModified' => $project->updated_at?->toAtomString(),
        'image' => $project->cover_url,
        'keywords' => $project->technologies->pluck('name')->implode(', '),
        'author' => $project->user ? [
            '@type' => 'Person',
            'name' => $project->user->name,
            'url' => route('profile.show', $project->user->username),
        ] : null,
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
</script>
@endpush

@section('content')
<header class="pp-nav">
    <a href="{{ route('landing') }}" class="pp-nav__brand" aria-label="Fluxa — inicio">
        <img src="{{ asset('img/logo-fluxa.png') }}" alt="Fluxa" height="22" />
    </a>

    <nav class="pp-nav__actions" aria-label="Acciones de cuenta">
        <a href="{{ route('login') }}" class="pp-btn pp-btn--ghost">Iniciar sesión</a>
        <a href="{{ route('register') }}" class="pp-btn pp-btn--primary">Crear cuenta</a>
    </nav>
</header>

<main class="pp-main">
    <article class="pp-article">
        <div class="pp-author">
            @if($project->user)
            <a href="{{ route('profile.show', $project->user->username) }}">
                <img src="{{ $project->user->avatar_url }}" alt="{{ $project->user->name }}" class="pp-author__avatar" />
            </a>
            <div>
                <a href="{{ route('profile.show', $project->user->username) }}" class="pp-author__name">{{ $project->user->name }}</a>
                <p class="pp-author__meta">
                    <span>&#64;{{ $project->user->username }}</span>
                    <span aria-hidden="true">·</span>
                    <time datetime="{{ $project->created_at?->toAtomString() }}">{{ $project->created_at?->translatedFormat('d M Y') }}</time>
                </p>
            </div>
            @else
            <div>
                <span class="pp-author__name">Usuario eliminado</span>
            </div>
            @endif
        </div>

        <h1 class="pp-title">{{ $project->title }}</h1>

        @if($project->technologies->isNotEmpty())
        <ul class="pp-tags" aria-label="Tecnologías del proyecto">
            @foreach($project->technologies as $technology)
            <li class="pp-tag">{{ $technology->name }}</li>
            @endforeach
        </ul>
        @endif

        @if($project->cover_url)
        <img
            src="{{ $project->cover_url }}"
            alt="Imagen del proyecto {{ $project->title }}"
            class="pp-cover"
            loading="eager" />
        @endif

        <div class="pp-content">{{ $project->content }}</div>

        <ul class="pp-stats" aria-label="Interacciones del proyecto">
            <li>
                <strong>{{ number_format($project->likes_count) }}</strong>
                <span>me gusta</span>
            </li>
            <li>
                <strong>{{ number_format($project->comments_count) }}</strong>
                <span>{{ $project->comments_count === 1 ? 'comentario' : 'comentarios' }}</span>
            </li>
            <li>
                <strong>{{ number_format($project->skillEndorsements->count()) }}</strong>
                <span>{{ $project->skillEndorsements->count() === 1 ? 'reconocimiento' : 'reconocimientos' }}</span>
            </li>
        </ul>

        <div class="pp-cta">
            <h2>¿Te gustó este proyecto?</h2>
            <p>Únete a Fluxa para comentar, dar reconocimientos y compartir tu propio progreso con la comunidad.</p>
            <div class="pp-cta__actions">
                <a href="{{ route('register') }}" class="pp-btn pp-btn--primary pp-btn--lg">Crear cuenta gratis</a>
                <a href="{{ route('login') }}" class="pp-btn pp-btn--ghost pp-btn--lg">Ya tengo cuenta</a>
            </div>
        </div>
    </article>

    @if($project->user)
    <aside class="pp-aside">
        <div class="pp-card">
            <img src="{{ $project->user->avatar_url }}" alt="{{ $project->user->name }}" class="pp-card__avatar" />
            <h2 class="pp-card__name">{{ $project->user->name }}</h2>
            <p class="pp-card__handle">&#64;{{ $project->user->username }}</p>

            @if($project->user->profile?->specialty)
            <p class="pp-card__specialty">{{ ucfirst($project->user->profile->specialty) }}</p>
            @endif

            @if($project->user->profile?->bio)
            <p class="pp-card__bio">{{ Str::limit($project->user->profile->bio, 140) }}</p>
            @endif

            <a href="{{ route('profile.show', $project->user->username) }}" class="pp-btn pp-btn--outline">Ver perfil</a>
        </div>

        <div class="pp-card pp-card--muted">
            <h2 class="pp-card__name">¿Construyes en público?</h2>
            <p class="pp-card__bio">Publica tus proyectos, recibe feedback y construye tu portafolio con la comunidad dev de Latinoamérica.</p>
            <a href="{{ route('register') }}" class="pp-btn pp-btn--primary">Empezar gratis</a>
        </div>
    </aside>
    @endif
</main>

<footer class="pp-footer">
    <p>© {{ date('Y') }} Fluxa · Hecho por y para la comunidad dev de Latinoamérica.</p>
</footer>
@endsection

@push('styles')
@vite('resources/css/projects/publicProject.css')
@endpush
