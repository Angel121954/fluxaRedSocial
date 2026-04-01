<?php

namespace App\Http\Controllers\Explore;

use App\Http\Controllers\Controller;
use App\Models\Profile;
use App\Models\Project;
use Illuminate\Support\Facades\Auth;

class ExploreController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $profile = Profile::where('user_id', $user->id)->first();

        $projects = $this->getProjects('trending');

        return view('explore.index', compact('profile', 'projects'));
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

    public function following()
    {
        $projects = $this->getProjects('following');

        if (request()->ajax()) {
            return view('components.project-list', compact('projects'))->render();
        }

        return view('explore.index', compact('projects'));
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
                    ->paginate(10);

            case 'recent':
                return $query->orderByDesc('created_at')
                    ->paginate(10);

            case 'following':
                $followingIds = Auth::user()->follows()->pluck('followed_id');

                return $query->whereIn('user_id', $followingIds)
                    ->orderByDesc('created_at')
                    ->paginate(10);

            default:
                return $query->orderByDesc('created_at')->paginate(10);
        }
    }
}
