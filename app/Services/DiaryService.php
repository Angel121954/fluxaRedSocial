<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Diary;
use App\Models\DiaryReport;
use App\Models\DiaryResponse;
use App\Models\DiaryResponseBookmark;
use App\Models\DiaryResponseComment;
use App\Models\DiaryResponseCommentLike;
use App\Models\DiaryResponseLike;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class DiaryService
{
    public function getActiveDiary(): ?Diary
    {
        return Diary::active()->withCount('responses')->first();
    }

    public function getResponses(int $diaryId, ?User $viewer, string $sort = 'top'): LengthAwarePaginator
    {
        $blockedIds = $viewer?->blockedUserIds() ?? collect();

        $query = DiaryResponse::where('diary_id', $diaryId)
            ->whereHas('user', fn ($query) => $query->visible())
            ->when($blockedIds->isNotEmpty(), fn ($query) => $query->whereNotIn('user_id', $blockedIds))
            ->with('user.profile')
            ->withCount(['likes', 'comments']);

        if ($viewer !== null) {
            $query->with([
                'likes' => fn ($q) => $q->where('user_id', $viewer->id),
                'bookmarks' => fn ($q) => $q->where('user_id', $viewer->id),
            ]);
        }

        return $query
            ->when($sort === 'top', fn ($q) => $q->orderByDesc('likes_count'))
            ->when($sort !== 'top', fn ($q) => $q->latest())
            ->paginate(10);
    }

    public function getRecentResponders(int $diaryId, ?User $viewer): Collection
    {
        $blockedIds = $viewer?->blockedUserIds() ?? collect();

        return DiaryResponse::where('diary_id', $diaryId)
            ->whereHas('user', fn ($query) => $query->visible())
            ->when($blockedIds->isNotEmpty(), fn ($query) => $query->whereNotIn('user_id', $blockedIds))
            ->with('user.profile')
            ->latest()
            ->take(3)
            ->get()
            ->pluck('user');
    }

    public function hasUserResponded(int $diaryId, ?int $userId): bool
    {
        if ($userId === null) {
            return false;
        }

        return DiaryResponse::where('diary_id', $diaryId)
            ->where('user_id', $userId)
            ->exists();
    }

    public function createOrUpdateResponse(Diary $diary, int $userId, string $content): DiaryResponse
    {
        $response = DiaryResponse::updateOrCreate(
            [
                'diary_id' => $diary->id,
                'user_id' => $userId,
            ],
            [
                'content' => $content,
            ]
        );

        if ($response->wasRecentlyCreated) {
            NotificationService::notifyMentions(
                $content,
                User::find($userId),
                '/diary',
                $response->id,
                'diary',
                [$userId],
            );
        }

        return $response;
    }

    public function responsesCount(int $diaryId): int
    {
        return DiaryResponse::where('diary_id', $diaryId)->count();
    }

    public function toggleLike(DiaryResponse $response, int $userId): array
    {
        $viewer = User::find($userId);

        if (! $viewer || ! $response->user?->isVisible() || $viewer->isBlockedWithId($response->user_id)) {
            throw new AuthorizationException('No puedes interactuar con esta respuesta.');
        }

        $liked = DB::transaction(function () use ($response, $userId): bool {
            $like = DiaryResponseLike::where('diary_response_id', $response->id)
                ->where('user_id', $userId)
                ->lockForUpdate()
                ->first();

            if ($like) {
                $like->delete();
                $response->decrement('likes_count');

                return false;
            }

            DiaryResponseLike::create([
                'diary_response_id' => $response->id,
                'user_id' => $userId,
            ]);
            $response->increment('likes_count');

            return true;
        });

        if ($liked && $response->user_id !== $userId) {
            NotificationService::notifyDiaryResponseLike(
                $response->user_id,
                $userId,
                $viewer->name,
            );
        }

        return [
            'liked' => $liked,
            'likes_count' => $response->fresh()->likes_count,
        ];
    }

    public function toggleBookmark(DiaryResponse $response, int $userId): array
    {
        $viewer = User::find($userId);

        if (! $viewer || ! $response->user?->isVisible() || $viewer->isBlockedWithId($response->user_id)) {
            throw new AuthorizationException('No puedes interactuar con esta respuesta.');
        }

        $bookmarked = DB::transaction(function () use ($response, $userId): bool {
            $bookmark = DiaryResponseBookmark::where('diary_response_id', $response->id)
                ->where('user_id', $userId)
                ->lockForUpdate()
                ->first();

            if ($bookmark) {
                $bookmark->delete();

                return false;
            }

            DiaryResponseBookmark::create([
                'diary_response_id' => $response->id,
                'user_id' => $userId,
            ]);

            return true;
        });

        if ($bookmarked && $response->user_id !== $userId) {
            NotificationService::notifyDiaryResponseBookmark(
                $response->user_id,
                $userId,
                User::find($userId)->name,
            );
        }

        return ['bookmarked' => $bookmarked];
    }

    public function createComment(DiaryResponse $response, int $userId, string $content, ?int $parentId): array
    {
        $author = User::find($userId);

        if (! $author || ! $response->user?->isVisible() || $author->isBlockedWithId($response->user_id)) {
            throw new AuthorizationException('No puedes interactuar con esta respuesta.');
        }

        if ($parentId !== null) {
            $parent = DiaryResponseComment::find($parentId);

            if (
                ! $parent
                || $parent->diary_response_id !== $response->id
                || $author->isBlockedWithId($parent->user_id)
            ) {
                throw new AuthorizationException('No puedes responder a este comentario.');
            }
        }

        $comment = DB::transaction(function () use ($response, $userId, $content, $parentId): DiaryResponseComment {
            $comment = DiaryResponseComment::create([
                'diary_response_id' => $response->id,
                'user_id' => $userId,
                'parent_id' => $parentId,
                'content' => $content,
            ]);

            $response->increment('comments_count');

            return $comment;
        });

        $comment->load('user.profile');
        $comment->user?->append('avatar_url');

        if ($response->user_id !== $userId) {
            NotificationService::notifyDiaryResponseComment(
                $response->user_id,
                $userId,
                $author->name,
            );
        }

        NotificationService::notifyMentions(
            $content,
            $author,
            '/diary',
            $response->id,
            'diary',
            [$userId, $response->user_id],
        );

        return [
            'comment' => $comment,
            'comments_count' => $response->loadCount('comments')->comments_count,
        ];
    }

    public function getComments(DiaryResponse $response, ?User $viewer): Collection
    {
        $blockedIds = $viewer?->blockedUserIds() ?? collect();
        $viewerId = $viewer?->id;

        $comments = $response->comments()
            ->whereNull('parent_id')
            ->whereHas('user', fn ($query) => $query->visible())
            ->when($blockedIds->isNotEmpty(), fn ($query) => $query->whereNotIn('user_id', $blockedIds))
            ->with([
                'user.profile',
                'children' => fn ($query) => $query
                    ->whereHas('user', fn ($query) => $query->visible())
                    ->when($blockedIds->isNotEmpty(), fn ($query) => $query->whereNotIn('user_id', $blockedIds)),
                'children.user.profile',
                'children.likes',
                'likes',
            ])
            ->latest()
            ->get();

        $comments->each(function (DiaryResponseComment $comment) use ($viewerId) {
            $comment->user?->append('avatar_url');
            $comment->is_liked = $comment->likes->contains('user_id', $viewerId);

            $comment->children->each(function (DiaryResponseComment $child) use ($viewerId) {
                $child->user?->append('avatar_url');
                $child->is_liked = $child->likes->contains('user_id', $viewerId);
            });
        });

        return $comments;
    }

    public function toggleCommentLike(DiaryResponseComment $comment, int $userId): bool
    {
        $viewer = User::find($userId);

        if (! $viewer || ! $comment->user?->isVisible() || $viewer->isBlockedWithId($comment->user_id)) {
            throw new AuthorizationException('No puedes interactuar con este comentario.');
        }

        $existing = DiaryResponseCommentLike::where('user_id', $userId)
            ->where('diary_response_comment_id', $comment->id)
            ->first();

        if ($existing) {
            $existing->delete();

            return false;
        }

        DiaryResponseCommentLike::create([
            'user_id' => $userId,
            'diary_response_comment_id' => $comment->id,
        ]);

        return true;
    }

    public function deleteComment(DiaryResponseComment $comment): int
    {
        $response = $comment->response;
        $commentsToDelete = 1 + $comment->children()->count();

        DB::transaction(function () use ($comment, $response, $commentsToDelete): void {
            $comment->delete();
            $response->decrement('comments_count', $commentsToDelete);
        });

        return $response->loadCount('comments')->comments_count;
    }

    public function report(DiaryResponse $response, int $userId, string $reason): bool
    {
        $repeated = DiaryReport::where('user_id', $userId)
            ->where('diary_response_id', $response->id)
            ->exists();

        if ($repeated) {
            return false;
        }

        DiaryReport::create([
            'user_id' => $userId,
            'diary_response_id' => $response->id,
            'reason' => $reason,
        ]);

        return true;
    }

    public function deleteResponse(DiaryResponse $response): void
    {
        $response->delete();
    }
}
