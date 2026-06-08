<?php

declare(strict_types=1);

namespace App\Http\Controllers\Feed;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

class FeedController extends Controller
{
    public function index()
    {
        return view('feed.index', [
            'projects' => $this->getFollowingProjects(),
        ]);
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

        return Project::with([
            'user.profile',
            'media',
            'technologies',
            'likes',
            'bookmarks',
            'skillEndorsements',
        ])
            ->where('parent_id', null)
            ->where('privacy', 'public')
            ->whereIn('user_id', $followingIds)
            ->orderByDesc('created_at')
            ->paginate(15);
    }
}
