@php $response->loadCount(['likes', 'comments']); @endphp
<div class="diary-response-card" data-response-id="{{ $response->id }}">
    <a href="{{ route('profile.show', $response->user->username) }}" class="diary-response-card__avatar-link">
        <img
            src="{{ $response->user->avatar_url }}"
            alt="{{ $response->user->name }}"
            class="diary-response-card__avatar"
        />
    </a>

    <div class="diary-response-card__body">
        <div class="diary-response-card__meta">
            <div class="diary-response-card__author-row">
                <a href="{{ route('profile.show', $response->user->username) }}" class="diary-response-card__name">
                    {{ $response->user->name }}
                </a>
                @if($response->user->is_verified)
                <span class="diary-response-card__verified" title="Verificado">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                        <polyline points="22 4 12 14.01 9 11.01"/>
                    </svg>
                </span>
                @endif
                <span class="diary-response-card__dot">·</span>
                <time
                    class="diary-response-card__time"
                    data-live-time="{{ $response->created_at->timestamp }}"
                    datetime="{{ $response->created_at->toISOString() }}"
                >{{ $response->created_at->diffForHumans() }}</time>
            </div>
        </div>

        <p class="diary-response-card__text">{{ $response->content }}</p>

        <div class="diary-response-card__actions">
            <button
                class="diary-action-btn diary-like-btn {{ $response->liked_by_auth ? 'is-liked' : '' }}"
                data-response-id="{{ $response->id }}"
                data-url="{{ route('diary.response.like', $response) }}"
                aria-label="Me gusta"
                aria-pressed="{{ $response->liked_by_auth ? 'true' : 'false' }}"
            >
                <svg width="15" height="15" viewBox="0 0 24 24" fill="{{ $response->liked_by_auth ? 'currentColor' : 'none' }}" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
                </svg>
                <span class="diary-like-btn__count">{{ number_format($response->likes_count, 0, ',', '.') }}</span>
            </button>

            <button
                class="diary-action-btn diary-comment-btn"
                data-response-id="{{ $response->id }}"
                aria-label="Comentarios"
            >
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                </svg>
                <span>{{ number_format($response->comments_count, 0, ',', '.') }}</span>
            </button>
        </div>
    </div>

    <div class="diary-response-card__aside">
        <button
            class="diary-icon-btn diary-bookmark-btn {{ $response->bookmarked_by_auth ? 'is-saved' : '' }}"
            data-response-id="{{ $response->id }}"
            data-url="{{ route('diary.response.bookmark', $response) }}"
            aria-label="{{ $response->bookmarked_by_auth ? 'Quitar guardado' : 'Guardar respuesta' }}"
            aria-pressed="{{ $response->bookmarked_by_auth ? 'true' : 'false' }}"
        >
            <svg width="15" height="15" viewBox="0 0 24 24" fill="{{ $response->bookmarked_by_auth ? 'currentColor' : 'none' }}" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"/>
            </svg>
        </button>

        <div class="diary-response-card__menu-wrap">
            <button
                class="diary-icon-btn diary-menu-btn"
                aria-label="Más opciones"
                aria-haspopup="true"
                aria-expanded="false"
                data-response-id="{{ $response->id }}"
            >
                <svg width="15" height="15" viewBox="0 0 24 24" fill="currentColor" stroke="none" aria-hidden="true">
                    <circle cx="5" cy="12" r="1.5"/>
                    <circle cx="12" cy="12" r="1.5"/>
                    <circle cx="19" cy="12" r="1.5"/>
                </svg>
            </button>
            <ul class="diary-response-menu" role="menu">
                @if(auth()->id() === $response->user_id)
                <li role="none">
                    <button
                        class="diary-response-menu__item diary-delete-btn"
                        role="menuitem"
                        data-response-id="{{ $response->id }}"
                        data-url="{{ route('diary.response.destroy', $response) }}"
                    >
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <polyline points="3 6 5 6 21 6"/>
                            <path d="M19 6l-1 14H6L5 6"/>
                            <path d="M10 11v6M14 11v6"/>
                            <path d="M9 6V4h6v2"/>
                        </svg>
                        Eliminar respuesta
                    </button>
                </li>
                @else
                <li role="none">
                    <button class="diary-response-menu__item" role="menuitem">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M4 15s1-1 4-1 5 2 8 2 4-1 4-1V3s-1 1-4 1-5-2-8-2-4 1-4 1z"/>
                            <line x1="4" y1="22" x2="4" y2="15"/>
                        </svg>
                        Reportar
                    </button>
                </li>
                @endif
            </ul>
        </div>
    </div>
</div>
