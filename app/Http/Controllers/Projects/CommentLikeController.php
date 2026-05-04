<?php

namespace App\Http\Controllers\Projects;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\CommentLike;
use App\Notifications\CreatesNotifications;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentLikeController extends Controller
{
    public function toggle(Request $request, Comment $comment)
    {
        try {
            $userId = Auth::id();

            if (!$userId) {
                return response()->json(['error' => 'Unauthenticated'], 401);
            }

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
                CreatesNotifications::notifyCommentLike(
                    $comment->user_id,
                    $userId,
                    Auth::user()->name,
                    $comment->id,
                    $comment->project_id,
                    $comment->project->title
                );
            }

            return response()->json([
                'success' => true,
                'liked' => true,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 500);
        }
    }
}
