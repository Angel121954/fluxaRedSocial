@props(['project'])

@php
$user = $project->user;
$timeAgo = $project->created_at->diffForHumans();
$isLiked = $project->isLikedBy(auth()->id());
$isBookmarked = $project->isBookmarkedBy(auth()->id());
@endphp

<div class="post-card" data-project-id="{{ $project->id }}">
    <div style="display: flex; justify-content: space-between">
        <div class="post-header">
            <a href="/profile/{{ $user->username ?? '/profile' }}">
                <img
                    src="{{ $user->avatar_url }}"
                    alt="{{ $user->username }}"
                    class="post-avatar"
                    loading="lazy" />
            </a>
            <div class="post-meta">
                <div class="post-author-row">
                    <span class="post-author">{{ $user->name }}</span>
                    @if($user->email_verified_at)
                    <div class="verify-badge">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    @endif
                </div>
                <div style="display: flex; align-items: center; gap: 0.375rem">
                    <span class="post-handle">{{ '@' . $user->username }}</span>
                    <span style="color: var(--ink-200)">·</span>
                    <span class="post-time">{{ $timeAgo }}</span>
                </div>
            </div>
        </div>
        <div class="drop-wrap">
            <button class="btn-icon post-menu-btn" data-project-id="{{ $project->id }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01" />
                </svg>
            </button>
            <div class="drop-menu" data-project-id="{{ $project->id }}">
                <button class="drop-item" data-action="bookmark" data-project-id="{{ $project->id }}">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" />
                    </svg>
                    <span>{{ $isBookmarked ? 'Quitar de favoritos' : 'Agregar a favoritos' }}</span>
                </button>
                <button class="drop-item" data-action="share" data-project-id="{{ $project->id }}">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z" />
                    </svg>
                    <span>Compartir</span>
                </button>
                <button class="drop-item" data-action="copy-link" data-project-id="{{ $project->id }}">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                    </svg>
                    <span>Copiar enlace</span>
                </button>
                <button class="drop-item" data-action="report" data-project-id="{{ $project->id }}">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9" />
                    </svg>
                    <span>Reportar</span>
                </button>
            </div>
        </div>
    </div>

    <h3 class="project-title">{{ $project->title }}</h3>
    <p class="post-content">{{ $project->content }}</p>

    @if($project->media->count() > 0)
    <x-project-media :media="$project->media" />
    @endif

    @if($project->technologies->count() > 0)
    <div class="post-tags">
        @foreach($project->technologies as $tech)
        <span class="post-tag">{{ $tech->name }}</span>
        @endforeach
    </div>
    @endif

    <div class="post-actions">
        <button class="post-action like-btn {{ $isLiked ? 'liked' : '' }}" data-project-id="{{ $project->id }}">
            <svg fill="{{ $isLiked ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
            </svg>
            <span class="like-count">{{ $project->likes_count }}</span>
        </button>
        <button class="post-action comment-btn" data-project-id="{{ $project->id }}">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
            </svg>
            <span>{{ $project->comments_count }}</span>
        </button>
    </div>
</div>