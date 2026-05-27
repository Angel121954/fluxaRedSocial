{{-- ═══════════════════════════════════════════════════════════
     resources/views/diary/index.blade.php
     Vista principal: Diario — una pregunta al día
     Vite entry: resources/js/diary/index.js
     CSS entry:  resources/css/diary/diary.css
══════════════════════════════════════════════════════════ --}}

@extends('layouts.app')

@section('title', 'Diario · Fluxa')

@push('styles')
@vite('resources/css/diary/diary.css')
@endpush

@section('content')

<x-topbar :profile="$profile" />

<div class="diary-page">

    {{-- ── Encabezado ─────────────────────────────────────────────── --}}
    <div class="diary-page-header">
        <div class="diary-page-header__left">
            <div class="diary-page-icon">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2" />
                    <line x1="16" y1="2" x2="16" y2="6" />
                    <line x1="8" y1="2" x2="8" y2="6" />
                    <line x1="3" y1="10" x2="21" y2="10" />
                    <rect x="7" y="14" width="4" height="4" rx="1" fill="currentColor" stroke="none" />
                </svg>
            </div>
            <div>
                <h1 class="diary-page-title">Diario</h1>
                <p class="diary-page-subtitle">Una pregunta cada día. Miles de perspectivas.</p>
            </div>
        </div>


    </div>

    @if(!empty($noDiary))
    <div class="diary-empty-state">
        <div class="diary-empty-state__icon">
            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <rect x="3" y="4" width="18" height="18" rx="2" ry="2" />
                <line x1="16" y1="2" x2="16" y2="6" />
                <line x1="8" y1="2" x2="8" y2="6" />
                <line x1="3" y1="10" x2="21" y2="10" />
            </svg>
        </div>
        <p class="diary-empty-state__label">No hay diario activo por ahora</p>
        <p class="diary-empty-state__sublabel">Vuelve pronto — una nueva pregunta llegará en las próximas horas.</p>
    </div>
    @else

    {{-- ── Tarjeta principal del Diario ──────────────────────────── --}}
    <div class="diary-card">

        {{-- Columna izquierda: contenido --}}
        <div class="diary-card__content">

            <div class="diary-badge">
                <span>Diario de hoy</span>
                <span class="diary-badge__dot">·</span>
                <span>#{{ $diary->question_number }}</span>
            </div>

            <h2 class="diary-question">
                {{ $diary->content }}
                @if($diary->emoji)
                <span class="diary-question__emoji">{{ $diary->emoji }}</span>
                @endif
            </h2>

            <div class="diary-countdown">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <circle cx="12" cy="12" r="10" />
                    <polyline points="12 6 12 12 16 14" />
                </svg>
                <span id="diary-countdown-label">Cierra en </span>
                <span
                    id="diary-countdown-timer"
                    class="diary-countdown__time"
                    data-closes-at="{{ $diary->closes_at?->timestamp ?? '' }}">
                    {{ gmdate('H:i:s', max(0, $diary->closes_at?->diffInSeconds(now()) ?? 0)) }}
                </span>
                <span id="diary-countdown-closed" class="diary-countdown__closed" style="display:none">
                    Diario cerrado — Nos vemos mañana con una nueva pregunta.
                </span>
            </div>

            {{-- Mensaje cuando el diario está cerrado --}}
            <div id="diary-reply-closed" class="diary-reply-closed" style="display:none">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M12 20h9" />
                    <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z" />
                </svg>
                <span>Gracias por compartir tu perspectiva. La próxima pregunta llega muy pronto.</span>
            </div>

            {{-- Formulario de respuesta --}}
            @auth
            <div id="diary-reply-box" class="diary-reply-box">
                <img
                    src="{{ Auth::user()?->avatar_url ?? '/img/default-avatar.png' }}"
                    alt="{{ $profile?->name ?? 'Usuario' }}"
                    class="diary-reply-box__avatar" />
                <div class="diary-reply-box__inner">
                    <textarea
                        id="diary-reply-input"
                        class="diary-reply-box__input"
                        placeholder="{{ $userHasResponded ? 'Ya respondiste — elimínala para volver a publicar' : 'Escribe tu respuesta...' }}"
                        maxlength="500"
                        rows="1"
                        {{ $userHasResponded ? 'disabled' : '' }}></textarea>
                </div>
                <button
                    id="diary-reply-btn"
                    class="diary-reply-box__btn"
                    data-diary-id="{{ $diary->id }}"
                    data-url="{{ route('diary.reply', $diary) }}"
                    {{ $userHasResponded ? 'disabled' : '' }}>
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <line x1="22" y1="2" x2="11" y2="13" />
                        <polygon points="22 2 15 22 11 13 2 9 22 2" />
                    </svg>
                    Publicar
                </button>
            </div>
            @else
            <a href="{{ route('login') }}" class="diary-reply-box diary-reply-box--guest">
                <span class="diary-reply-box__placeholder">Inicia sesión para responder...</span>
                <span class="diary-reply-box__btn">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <line x1="22" y1="2" x2="11" y2="13" />
                        <polygon points="22 2 15 22 11 13 2 9 22 2" />
                    </svg>
                    Publicar
                </span>
            </a>
            @endauth

            {{-- Conteo de respuestas --}}
            <div class="diary-respondents">
                @if($diary->responses_count > 0)
                <div class="diary-respondents__avatars">
                    @foreach($recentResponders->take(3) as $responder)
                    <img
                        src="{{ $responder->avatar_url }}"
                        alt="{{ $responder->name }}"
                        class="diary-respondents__avatar" />
                    @endforeach
                </div>
                @endif
                @if(number_format($diary->responses_count, 0, ',', '.') != 0)
                <span class="diary-respondents__count">
                    {{ number_format($diary->responses_count, 0, ',', '.') }}
                    {{ $diary->responses_count === 1 ? 'persona ya respondió' : 'personas ya respondieron' }}
                </span>
                @endif
            </div>
        </div>

    </div>{{-- /.diary-card --}}

    {{-- ── Respuestas ─────────────────────────────────────────────── --}}
    <div class="diary-responses-header">
        <h3 class="diary-responses-title">Respuestas</h3>

        <div class="diary-sort-wrap">
            <button class="diary-sort-btn" id="diary-sort-toggle" aria-expanded="false" aria-haspopup="listbox">
                {{ request('sort', 'top') === 'top' ? 'Más valoradas' : 'Más recientes' }}
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <polyline points="6 9 12 15 18 9" />
                </svg>
            </button>
            <ul class="diary-sort-dropdown" id="diary-sort-dropdown" role="listbox" aria-label="Ordenar respuestas">
                <li role="option" aria-selected="{{ request('sort', 'top') === 'top' ? 'true' : 'false' }}">
                    <a href="{{ route('diary.index', ['sort' => 'top']) }}" class="diary-sort-option {{ request('sort', 'top') === 'top' ? 'active' : '' }}">
                        Más valoradas
                    </a>
                </li>
                <li role="option" aria-selected="{{ request('sort') === 'recent' ? 'true' : 'false' }}">
                    <a href="{{ route('diary.index', ['sort' => 'recent']) }}" class="diary-sort-option {{ request('sort') === 'recent' ? 'active' : '' }}">
                        Más recientes
                    </a>
                </li>
            </ul>
        </div>
    </div>

    {{-- Lista de respuestas --}}
    <div class="diary-responses-list" id="diary-responses-list">

        @forelse($responses as $response)
        <div class="diary-response-card" data-response-id="{{ $response->id }}">
            <a href="{{ route('profile.show', $response->user?->username ?? '#') }}" class="diary-response-card__avatar-link">
                <img
                    src="{{ $response->user?->avatar_url ?? '/img/default-avatar.png' }}"
                    alt="{{ $response->user?->name ?? 'Usuario' }}"
                    class="diary-response-card__avatar" />
            </a>

            <div class="diary-response-card__body">
                <div class="diary-response-card__meta">
                    <div class="diary-response-card__author-row">
                        <a href="{{ route('profile.show', $response->user?->username ?? '#') }}" class="diary-response-card__name">
                            {{ $response->user?->name ?? 'Usuario' }}
                        </a>
                        @if($response->user?->is_verified)
                        <span class="diary-response-card__verified" title="Verificado">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" />
                                <polyline points="22 4 12 14.01 9 11.01" />
                            </svg>
                        </span>
                        @endif
                        <span class="diary-response-card__dot">·</span>
                        <time
                            class="diary-response-card__time"
                            data-live-time="{{ $response->created_at?->timestamp ?? '' }}"
                            datetime="{{ $response->created_at?->toISOString() ?? '' }}">{{ $response->created_at?->diffForHumans() ?? '' }}</time>
                    </div>
                </div>

                <p class="diary-response-card__text">{{ $response->content }}</p>

                <div class="diary-response-card__actions">
                    {{-- Like --}}
                    <button
                        class="diary-action-btn diary-like-btn {{ $response->liked_by_auth ? 'is-liked' : '' }}"
                        data-response-id="{{ $response->id }}"
                        data-url="{{ route('diary.response.like', $response) }}"
                        aria-label="Me gusta"
                        aria-pressed="{{ $response->liked_by_auth ? 'true' : 'false' }}">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="{{ $response->liked_by_auth ? 'currentColor' : 'none' }}" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z" />
                        </svg>
                        <span class="diary-like-btn__count">{{ number_format($response->likes_count, 0, ',', '.') }}</span>
                    </button>

                    {{-- Comentarios --}}
                    <button
                        class="diary-action-btn diary-comment-btn"
                        data-response-id="{{ $response->id }}"
                        aria-label="Comentarios">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z" />
                        </svg>
                        <span>{{ number_format($response->comments_count, 0, ',', '.') }}</span>
                    </button>
                </div>
            </div>

            {{-- Acciones secundarias --}}
            <div class="diary-response-card__aside">
                <button
                    class="diary-icon-btn diary-bookmark-btn {{ $response->bookmarked_by_auth ? 'is-saved' : '' }}"
                    data-response-id="{{ $response->id }}"
                    data-url="{{ route('diary.response.bookmark', $response) }}"
                    aria-label="{{ $response->bookmarked_by_auth ? 'Quitar guardado' : 'Guardar respuesta' }}"
                    aria-pressed="{{ $response->bookmarked_by_auth ? 'true' : 'false' }}">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="{{ $response->bookmarked_by_auth ? 'currentColor' : 'none' }}" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z" />
                    </svg>
                </button>

                <div class="diary-response-card__menu-wrap">
                    <button
                        class="diary-icon-btn diary-menu-btn"
                        aria-label="Más opciones"
                        aria-haspopup="true"
                        aria-expanded="false"
                        data-response-id="{{ $response->id }}">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="currentColor" stroke="none" aria-hidden="true">
                            <circle cx="5" cy="12" r="1.5" />
                            <circle cx="12" cy="12" r="1.5" />
                            <circle cx="19" cy="12" r="1.5" />
                        </svg>
                    </button>
                    <ul class="diary-response-menu" role="menu">
                        @if(auth()->id() === $response->user_id)
                        <li role="none">
                            <button
                                class="diary-response-menu__item diary-delete-btn"
                                role="menuitem"
                                data-response-id="{{ $response->id }}"
                                data-url="{{ route('diary.response.destroy', $response) }}">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <polyline points="3 6 5 6 21 6" />
                                    <path d="M19 6l-1 14H6L5 6" />
                                    <path d="M10 11v6M14 11v6" />
                                    <path d="M9 6V4h6v2" />
                                </svg>
                                Eliminar respuesta
                            </button>
                        </li>
                        @else
                        <li role="none">
                            <button
                                class="diary-response-menu__item"
                                role="menuitem"
                                data-diary-report="{{ $response->id }}">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <path d="M4 15s1-1 4-1 5 2 8 2 4-1 4-1V3s-1 1-4 1-5-2-8-2-4 1-4 1z" />
                                    <line x1="4" y1="22" x2="4" y2="15" />
                                </svg>
                                Reportar
                            </button>
                        </li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>
        @empty
        <div class="diary-empty">
            <div class="diary-empty__icon" aria-hidden="true">
                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 20h9" />
                    <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z" />
                    <path d="M12 20h9" />
                </svg>
            </div>
            <div class="diary-empty__text">
                <p class="diary-empty__label">Aún no hay respuestas</p>
                <p class="diary-empty__sublabel">Sé la primera persona en compartir tu perspectiva hoy.</p>
            </div>
        </div>
        @endforelse

    </div>{{-- /.diary-responses-list --}}

    {{-- Paginación --}}
    @if($responses->hasMorePages())
    <div class="diary-load-more-wrap">
        <button
            class="diary-load-more-btn"
            id="diary-load-more"
            data-next-url="{{ $responses->nextPageUrl() }}">
            Cargar más respuestas
        </button>
    </div>
    @endif

</div>{{-- /.diary-page --}}
@endif

<x-modal-comments />
<x-diary-report-modal />

@endsection

@push('scripts')
@vite('resources/js/diary/index.js')
@vite('resources/js/diary/commentModal.js')
@vite('resources/js/diary/reportModal.js')
@endpush

@push('styles')
@vite('resources/css/shared/modal.css')
@vite('resources/css/core/explore.css')
@endpush