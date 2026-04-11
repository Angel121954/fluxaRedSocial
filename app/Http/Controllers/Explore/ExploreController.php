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
        $profile = Profile::where('user_id', Auth::id())->first();
        $technology = Technology::where('slug', $slug)->firstOrFail();

        $projects = Project::with(['user.profile', 'media', 'technologies'])
            ->where('parent_id', null)
            ->where('privacy', 'public')
            ->whereHas('technologies', function ($query) use ($technology) {
                $query->where('technologies.id', $technology->id);
            })
            ->orderByDesc('likes_count')
            ->orderByDesc('comments_count')
            ->paginate(15);

        $topTechnologies = Technology::withCount('projects')
            ->orderByDesc('projects_count')
            ->limit(15)
            ->get();

        return view('explore.index', compact('profile', 'projects', 'topTechnologies', 'technology'));
    }

    public function search(Request $request)
    {
        $query = $request->get('q', '');
        $profile = Profile::where('user_id', Auth::id())->first();

        $projects = Project::with(['user.profile', 'media', 'technologies'])
            ->where('parent_id', null)
            ->where('privacy', 'public')
            ->where('title', 'like', "%{$query}%")
            ->orderByDesc('likes_count')
            ->orderByDesc('comments_count')
            ->paginate(15);

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
        $query = Project::with(['user.profile', 'media', 'technologies'])
            ->where('parent_id', null)
            ->where('privacy', 'public');

        switch ($type) {
            case 'trending':
                return $query->orderByDesc('likes_count')
                    ->orderByDesc('comments_count')
                    ->paginate(15);

            case 'recent':
                return $query->orderByDesc('created_at')
                    ->paginate(15);

            default:
                return $query->orderByDesc('created_at')->paginate(15);
        }
    }
}
