@props(['project'])

@php
$user = $project->user;
$profile = $user->profile;
$timeAgo = $project->created_at->diffForHumans();
$isLiked = $project->isLikedBy(auth()->id());
@endphp

<div class="post-card" data-project-id="{{ $project->id }}">
    <div style="display: flex; justify-content: space-between">
        <div class="post-header">
            <img
                src="{{ $profile->avatar ?? 'https://api.dicebear.com/7.x/initials/svg?seed=' . strtolower($user->username) . '&backgroundColor=12b3b6' }}"
                alt="{{ $user->name }}"
                class="post-avatar" />
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
        <button class="post-menu">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01" />
            </svg>
        </button>
    </div>

    <h3 class="project-title">{{ $project->title }}</h3>
    <p class="post-content">{{ $project->content }}</p>

    @if($project->media->count() > 0)
    @foreach($project->media as $media)
    <img src="{{ $media->media_url }}" alt="{{ $media->all_text ?? 'Project image' }}" class="post-image" />
    @endforeach
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