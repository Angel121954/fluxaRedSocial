<?php

namespace App\Http\Controllers\Feed;

use App\Http\Controllers\Controller;
use App\Models\Profile;
use App\Models\Project;
use App\Models\Technology;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

class FeedController extends Controller
{
    public function index()
    {
        $profile = Profile::where('user_id', Auth::id())->first();
        $projects = $this->getFollowingProjects();
        $topTechnologies = Technology::withCount('projects')
            ->orderByDesc('projects_count')
            ->limit(15)
            ->get();

        return view('feed.index', compact('profile', 'projects', 'topTechnologies'));
    }

    public function paginate()
    {
        $projects = $this->getFollowingProjects();

        if (request()->ajax()) {
            return view('components.project-list', compact('projects'))->render();
        }

        return response()->json(['data' => $projects]);
    }

    private function getFollowingProjects()
    {
        $followingIds = Auth::user()->follows()->pluck('followed_id');

        if ($followingIds->isEmpty()) {
            $emptyPaginator = new LengthAwarePaginator([], 0, 15);

            return $emptyPaginator;
        }

        return Project::with(['user.profile', 'media', 'technologies'])
            ->where('parent_id', null)
            ->where('privacy', 'public')
            ->whereIn('user_id', $followingIds)
            ->orderByDesc('created_at')
            ->paginate(15);
    }
}
