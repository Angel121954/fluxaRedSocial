<?php

declare(strict_types=1);

namespace App\Http\Controllers\Projects;

use App\Http\Controllers\Controller;
use App\Http\Requests\Project\StoreCommentRequest;
use App\Models\Comment;
use App\Models\Project;
use App\Services\CommentService;
use Illuminate\Http\JsonResponse;

class CommentController extends Controller
{
    public function __construct(
        protected CommentService $commentService,
    ) {}

    public function store(StoreCommentRequest $request, Project $project): JsonResponse
    {
        $this->authorize('comment', $project);

        $parentId = $request->validated('parent_id');

        $comment = $this->commentService->create(
            $project,
            $request->user(),
            $request->validated('content'),
            $parentId !== null ? (int) $parentId : null,
        );

        return response()->json([
            'success' => true,
            'comment' => $comment->load('user.profile')->setRelation('user',
                $comment->user ? $comment->user->append('avatar_url') : null
            ),
        ]);
    }

    public function index(Project $project): JsonResponse
    {
        $this->authorize('view', $project);

        $comments = $project->comments()
            ->whereNull('parent_id')
            ->with(['user.profile', 'children.user.profile', 'children.likes', 'likes'])
            ->latest()
            ->paginate(20);

        $userId = auth()->id();

        $comments->getCollection()->each(function ($comment) use ($userId) {
            if ($comment->user) {
                $comment->user->append('avatar_url');
            }
            $comment->setAttribute('is_liked', $comment->likes->contains('user_id', $userId));
            $comment->children->each(function ($child) use ($userId) {
                if ($child->user) {
                    $child->user->append('avatar_url');
                }
                $child->setAttribute('is_liked', $child->likes->contains('user_id', $userId));
            });
        });

        return response()->json([
            'comments' => $comments->items(),
            'has_more' => $comments->hasMorePages(),
            'next_page' => $comments->currentPage() + 1,
        ]);
    }

    public function destroy(Comment $comment): JsonResponse
    {
        $this->authorize('delete', $comment);

        $this->commentService->delete($comment);

        return response()->json(['success' => true]);
    }
}
