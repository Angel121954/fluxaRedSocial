<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Project;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ContentController extends Controller
{
    public function index(): View
    {
        $projects = Project::with([
            'user' => fn ($q) => $q->select('id', 'name', 'username')->with('profile'),
        ])
            ->select('id', 'user_id', 'title', 'privacy', 'likes_count', 'comments_count', 'deleted_at', 'created_at')
            ->withTrashed()
            ->latest()
            ->paginate(25);

        $comments = Comment::with([
            'user' => fn ($q) => $q->select('id', 'name', 'username')->with('profile'),
            'project:id,title',
        ])
            ->select('id', 'user_id', 'project_id', 'content', 'created_at')
            ->latest()
            ->paginate(25);

        $counts = [
            'projects' => Project::count(),
            'projects_trashed' => Project::onlyTrashed()->count(),
            'comments' => Comment::count(),
        ];

        return view('admin.content.index', compact('projects', 'comments', 'counts'));
    }

    public function deleteProject(Project $project): RedirectResponse
    {
        $project->delete();

        return redirect()->route('admin.content.index')
            ->with('success', 'Proyecto eliminado correctamente.');
    }

    public function restoreProject(Project $project): RedirectResponse
    {
        $project->restore();

        return redirect()->route('admin.content.index')
            ->with('success', 'Proyecto restaurado correctamente.');
    }

    public function deleteComment(Comment $comment): RedirectResponse
    {
        $comment->delete();

        return redirect()->route('admin.content.index')
            ->with('success', 'Comentario eliminado correctamente.');
    }
}
