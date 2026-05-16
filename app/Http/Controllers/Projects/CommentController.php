<?php

namespace App\Http\Controllers\Projects;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCommentRequest;
use App\Models\Project;
use App\Models\Comment;
use App\Notifications\CreatesNotifications;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public function store(StoreCommentRequest $request, Project $project)
    {
        $this->authorize('comment', $project);

        $comment = $project->comments()->create([
            'user_id' => Auth::id(),
            'parent_id' => $request->parent_id,
            'content' => $request->content,
        ]);

        $project->increment('comments_count');

        $currentUserId = Auth::id();
        $currentUserName = Auth::user()->name;

        if ($request->parent_id) {
            $parentComment = Comment::find($request->parent_id);
            if ($parentComment && $parentComment->user_id !== $currentUserId) {
                CreatesNotifications::notifyCommentReply(
                    $parentComment->user_id,
                    $currentUserId,
                    $currentUserName,
                    $project->id,
                    $project->title
                );
            }
        } elseif ($project->user_id !== $currentUserId) {
            CreatesNotifications::notifyProjectComment(
                $project->user_id,
                $currentUserId,
                $currentUserName,
                $project->id,
                $project->title
            );
        }

        return response()->json([
            'success' => true,
            'comment' => $comment->load('user')->setRelation('user',
                $comment->user ? $comment->user->append('avatar_url') : null
            ),
        ]);
    }

    public function index(Project $project)
    {
        $comments = $project->comments()
            ->whereNull('parent_id')
            ->with(['user', 'children.user', 'children.likes', 'likes'])
            ->latest()
            ->get();

        $userId = auth()->id();

        $comments->each(function ($comment) use ($userId) {
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
            'comments' => $comments,
        ]);
    }

    public function destroy(Comment $comment)
    {
        $this->authorize('delete', $comment);

        $project = $comment->project;
        $commentsToDelete = 1 + $comment->children()->count();
        $comment->delete();

        $project->decrement('comments_count', $commentsToDelete);

        return response()->json(['success' => true]);
    }
}
