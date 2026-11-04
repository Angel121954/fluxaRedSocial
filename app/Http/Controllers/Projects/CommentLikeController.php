<?php

declare(strict_types=1);

namespace App\Http\Controllers\Projects;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\CommentLike;
use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CommentLikeController extends Controller
{
    public function toggle(Request $request, Comment $comment): JsonResponse
    {
        $project = $comment->project;

        if (! $project) {
            abort(404);
        }

        $this->authorize('view', $project);

        $user = $request->user();
        $userId = $user->id;

        $existingLike = CommentLike::where('user_id', $userId)
            ->where('comment_id', $comment->id)
            ->first();

        if ($existingLike) {
            $existingLike->delete();

            return response()->json([
                'success' => true,
                'liked' => false,
            ]);
        }

        CommentLike::create([
            'user_id' => $userId,
            'comment_id' => $comment->id,
        ]);

        if ($comment->user_id !== $userId) {
            NotificationService::notifyCommentLike(
                $comment->user_id,
                $userId,
                $user->name,
                $comment->id,
                $comment->project_id,
                $comment->project->title
            );
        }

        return response()->json([
            'success' => true,
            'liked' => true,
        ]);
    }
}
