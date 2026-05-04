<?php

namespace App\Http\Controllers\Projects;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Comment;
use App\Notifications\CreatesNotifications;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public function store(Request $request, Project $project)
    {
        $request->validate([
            'content' => 'required|string|max:1000',
            'parent_id' => 'nullable|exists:comments,id',
        ]);

        if ($project->privacy !== 'public' && $project->user_id !== Auth::id()) {
            abort(403, 'No puedes comentar en este proyecto');
        }

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
            ->with(['user', 'children.user'])
            ->latest()
            ->get();

        // Append avatar_url and is_liked
        $comments->each(function ($comment) {
            if ($comment->user) {
                $comment->user->append('avatar_url');
            }
            $comment->setAttribute('is_liked', $comment->is_liked);
            $comment->children->each(function ($child) {
                if ($child->user) {
                    $child->user->append('avatar_url');
                }
                $child->setAttribute('is_liked', $child->is_liked);
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
