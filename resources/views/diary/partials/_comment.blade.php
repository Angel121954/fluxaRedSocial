<div class="diary-comment" data-comment-id="{{ $comment->id }}">
    <img
        src="{{ $comment->user->avatar_url }}"
        alt="{{ $comment->user->name }}"
        class="diary-comment__avatar"
    />
    <div class="diary-comment__body">
        <div class="diary-comment__meta">
            <span class="diary-comment__name">{{ $comment->user->name }}</span>
            <span class="diary-comment__dot">·</span>
            <time class="diary-comment__time" datetime="{{ $comment->created_at->toISOString() }}">
                {{ $comment->created_at->diffForHumans() }}
            </time>
        </div>
        <p class="diary-comment__text">{{ $comment->content }}</p>
    </div>
</div>
