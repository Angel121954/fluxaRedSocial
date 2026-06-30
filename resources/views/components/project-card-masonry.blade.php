@props(['project'])

@php
$timeAgo = $project->created_at?->diffForHumans() ?? '';
$isLiked = auth()->check() ? ($project->likes?->contains('user_id', auth()->id()) ?? false) : false;
$isBookmarked = auth()->check() ? ($project->bookmarks?->contains('user_id', auth()->id()) ?? false) : false;

$firstMedia = $project->media?->first();
$mediaCount = $project->media?->count() ?? 0;
$coverUrl = null;
if ($firstMedia) {
    $cloud = config('cloudinary.cloud_name');
    $coverUrl = $firstMedia->public_id
        ? "https://res.cloudinary.com/{$cloud}/image/upload/c_fill,g_auto,q_auto:good,f_auto,w_600,h_500/{$firstMedia->public_id}"
        : $firstMedia->media_url;
}
@endphp

<div class="msn-card" data-project-id="{{ $project->id }}">
    @if($coverUrl)
    <a href="{{ route('projects.show', $project->id) }}" class="msn-card-img-link">
        <div class="msn-card-img-wrap">
            <img
                src="{{ $coverUrl }}"
                alt="{{ $project->title }}"
                class="msn-card-img"
                loading="lazy" />
        </div>
    </a>
    @else
    <div class="msn-card-img-placeholder">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
        </svg>
    </div>
    @endif

    <div class="msn-card-body">
        <div class="msn-card-header">
            <a href="{{ route('projects.show', $project->id) }}" class="msn-card-title-link">
                <h3 class="msn-card-title">{{ $project->title }}</h3>
            </a>

            <div class="drop-wrap">
                <button class="msn-card-menu" data-project-id="{{ $project->id }}" aria-label="Más opciones">
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
                    @if(auth()->id() === $project->user_id)
                    <button class="drop-item" data-action="edit" data-project-id="{{ $project->id }}">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        <span>Editar</span>
                    </button>
                    @endif
                    @if(auth()->id() === $project->user_id && request()->routeIs('profile*'))
                    <button class="drop-item" data-action="delete" data-project-id="{{ $project->id }}">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        <span>Eliminar</span>
                    </button>
                    @endif
                </div>
            </div>
        </div>

        @if($project->content)
        <p class="msn-card-desc">{{ Str::limit($project->content, 120) }}</p>
        @endif

        @if($project->technologies?->count() > 0)
        <div class="msn-card-tags">
            @foreach($project->technologies->take(3) as $tech)
            <span class="msn-tag">{{ $tech->name }}</span>
            @endforeach
            @if($project->technologies->count() > 3)
            <span class="msn-tag msn-tag-more">+{{ $project->technologies->count() - 3 }}</span>
            @endif
        </div>
        @endif

        <div class="msn-card-footer">
            <div class="msn-card-actions">
                <button class="msn-action like-btn {{ $isLiked ? 'liked' : '' }}" data-project-id="{{ $project->id }}">
                    <svg fill="{{ $isLiked ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                    </svg>
                    <span class="like-count">{{ $project->likes_count }}</span>
                </button>
                <button class="msn-action comment-btn" data-project-id="{{ $project->id }}">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                    </svg>
                    <span>{{ $project->comments_count }}</span>
                </button>
                <span class="msn-time">{{ $timeAgo }}</span>
            </div>
        </div>
    </div>
</div>
