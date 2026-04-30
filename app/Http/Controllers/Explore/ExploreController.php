<?php

namespace App\Http\Controllers\Explore;

use App\Http\Controllers\Controller;
use App\Models\Profile;
use App\Models\Project;
use App\Models\Technology;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExploreController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $profile = Profile::where('user_id', $user->id)->first();

        $projects = $this->getProjects('trending');
        $topTechnologies = Technology::withCount('projects')
            ->orderByDesc('projects_count')
            ->limit(15)
            ->get();

        return view('explore.index', compact('profile', 'projects', 'topTechnologies'));
    }

    public function trending()
    {
        $profile = Profile::where('user_id', Auth::id())->first();
        $projects = $this->getProjects('trending');
        $topTechnologies = Technology::withCount('projects')
            ->orderByDesc('projects_count')
            ->limit(15)
            ->get();

        if (request()->ajax()) {
            return view('components.project-list', compact('projects'))->render();
        }

        return view('explore.index', compact('profile', 'projects', 'topTechnologies'));
    }

    public function recent()
    {
        $profile = Profile::where('user_id', Auth::id())->first();
        $projects = $this->getProjects('recent');
        $topTechnologies = Technology::withCount('projects')
            ->orderByDesc('projects_count')
            ->limit(15)
            ->get();

        if (request()->ajax()) {
            return view('components.project-list', compact('projects'))->render();
        }

        return view('explore.index', compact('profile', 'projects', 'topTechnologies'));
    }

    public function topic($slug)
    {
        $userId = Auth::id();
        $profile = Profile::where('user_id', $userId)->first();
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
            ->whereHas('technologies', function ($query) use ($technology) {
                $query->where('technologies.id', $technology->id);
            })
            ->orderByDesc('likes_count')
            ->orderByDesc('comments_count')
            ->paginate(15);

        // Precomputar estados
        $projects->each(function ($project) use ($userId) {
            $project->precomputed_is_liked = $project->likes->isNotEmpty();
            $project->precomputed_is_bookmarked = $project->bookmarks->isNotEmpty();
            $project->precomputed_user_endorsement = $project->skillEndorsements
                ->where('user_id', $userId)
                ->first()?->skill_type;
        });

        $topTechnologies = Technology::withCount('projects')
            ->orderByDesc('projects_count')
            ->limit(15)
            ->get();

        return view('explore.index', compact('profile', 'projects', 'topTechnologies', 'technology'));
    }

    public function search(Request $request)
    {
        $userId = Auth::id();
        $query = $request->get('q', '');
        $profile = Profile::where('user_id', $userId)->first();

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

        // Precomputar estados
        $projects->each(function ($project) use ($userId) {
            $project->precomputed_is_liked = $project->likes->isNotEmpty();
            $project->precomputed_is_bookmarked = $project->bookmarks->isNotEmpty();
            $project->precomputed_user_endorsement = $project->skillEndorsements
                ->where('user_id', $userId)
                ->first()?->skill_type;
        });

        $topTechnologies = Technology::withCount('projects')
            ->orderByDesc('projects_count')
            ->limit(15)
            ->get();

        if ($request->ajax()) {
            return view('components.project-list', compact('projects'))->render();
        }

        return view('explore.index', compact('profile', 'projects', 'topTechnologies'));
    }

    private function getProjects($type)
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

        // Precomputar estados
        $projects->each(function ($project) use ($userId) {
            $project->precomputed_is_liked = $project->likes->isNotEmpty();
            $project->precomputed_is_bookmarked = $project->bookmarks->isNotEmpty();
            $project->precomputed_user_endorsement = $project->skillEndorsements
                ->where('user_id', $userId)
                ->first()?->skill_type;
        });

        return $projects;
    }
}
