<?php

declare(strict_types=1);

namespace App\Http\Controllers\Explore;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Technology;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExploreController extends Controller
{
    public function index()
    {
        return view('explore.index', [
            'projects' => $this->getProjects('trending'),
        ]);
    }

    public function trending()
    {
        $projects = $this->getProjects('trending');

        if (request()->ajax()) {
            return view('components.project-list', compact('projects'))->render();
        }

        return view('explore.index', compact('projects'));
    }

    public function recent()
    {
        $projects = $this->getProjects('recent');

        if (request()->ajax()) {
            return view('components.project-list', compact('projects'))->render();
        }

        return view('explore.index', compact('projects'));
    }

    public function topic(string $slug)
    {
        $userId = Auth::id();
        $technology = Technology::where('slug', $slug)->firstOrFail();

        $projects = Project::with([
            'user.profile',
            'media',
            'technologies',
            'likes' => fn ($q) => $q->where('user_id', $userId),
            'bookmarks' => fn ($q) => $q->where('user_id', $userId),
            'skillEndorsements',
        ])
            ->where('parent_id', null)
            ->where('privacy', 'public')
            ->whereHas('technologies', fn ($q) => $q->where('technologies.id', $technology->id))
            ->orderByDesc('likes_count')
            ->orderByDesc('comments_count')
            ->paginate(15);

        $this->precomputeStates($projects, $userId);

        return view('explore.index', compact('projects', 'technology'));
    }

    public function search(Request $request)
    {
        $userId = Auth::id();
        $query = $request->get('q', '');

        $projects = Project::with([
            'user.profile',
            'media',
            'technologies',
            'likes' => fn ($q) => $q->where('user_id', $userId),
            'bookmarks' => fn ($q) => $q->where('user_id', $userId),
            'skillEndorsements',
        ])
            ->where('parent_id', null)
            ->where('privacy', 'public')
            ->where('title', 'like', "%{$query}%")
            ->orderByDesc('likes_count')
            ->orderByDesc('comments_count')
            ->paginate(15);

        $this->precomputeStates($projects, $userId);

        if ($request->ajax()) {
            return view('components.project-list', compact('projects'))->render();
        }

        return view('explore.index', compact('projects'));
    }

    private function precomputeStates($projects, int $userId): void
    {
        $projects->each(function ($project) use ($userId) {
            $project->precomputed_is_liked = $project->likes->isNotEmpty();
            $project->precomputed_is_bookmarked = $project->bookmarks->isNotEmpty();
            $project->precomputed_user_endorsement = $project->skillEndorsements
                ->where('user_id', $userId)
                ->first()?->skill_type;
        });
    }

    private function getProjects(string $type)
    {
        $userId = Auth::id();
        $query = Project::with([
            'user.profile',
            'media',
            'technologies',
            'likes' => fn ($q) => $q->where('user_id', $userId),
            'bookmarks' => fn ($q) => $q->where('user_id', $userId),
            'skillEndorsements',
        ])
            ->where('parent_id', null)
            ->where('privacy', 'public');

        $projects = match ($type) {
            'trending' => $query->orderByDesc('likes_count')
                ->orderByDesc('comments_count')
                ->paginate(15),
            'recent' => $query->orderByDesc('created_at')
                ->paginate(15),
            default => $query->orderByDesc('created_at')->paginate(15),
        };

        $this->precomputeStates($projects, $userId);

        return $projects;
    }
}
