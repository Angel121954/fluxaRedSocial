<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Comment;
use App\Models\Project;
use App\Models\User;
use Illuminate\Support\Facades\DB;

final class CommentService
{
    public function create(Project $project, User $author, string $content, ?int $parentId = null): Comment
    {
        $comment = DB::transaction(function () use ($project, $author, $content, $parentId): Comment {
            $comment = $project->comments()->create([
                'user_id' => $author->id,
                'parent_id' => $parentId,
                'content' => $content,
            ]);

            $project->increment('comments_count');

            return $comment;
        });

        $this->notify($project, $comment, $author);

        return $comment;
    }

    public function delete(Comment $comment): void
    {
        $project = $comment->project;
        $commentsToDelete = 1 + $comment->children()->count();

        DB::transaction(function () use ($comment, $project, $commentsToDelete): void {
            $comment->delete();
            $project->decrement('comments_count', $commentsToDelete);
        });
    }

    private function notify(Project $project, Comment $comment, User $author): void
    {
        if ($comment->parent_id) {
            $parent = $comment->parent;

            if ($parent && $parent->user_id !== $author->id) {
                NotificationService::notifyCommentReply(
                    $parent->user_id,
                    $author->id,
                    $author->name,
                    $project->id,
                    $project->title,
                );
            }

            return;
        }

        if ($project->user_id !== $author->id) {
            NotificationService::notifyProjectComment(
                $project->user_id,
                $author->id,
                $author->name,
                $project->id,
                $project->title,
            );
        }
    }
}
