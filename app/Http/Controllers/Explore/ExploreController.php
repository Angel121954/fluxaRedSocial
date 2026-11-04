<?php

declare(strict_types=1);

namespace App\Http\Controllers\Explore;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Technology;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExploreController extends Controller
{
    public function index(Request $request)
    {
        if ($request->filled('q')) {
            return redirect()->route('explore.search', ['q' => $request->query('q')]);
        }

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
        $blockedIds = Auth::user()?->blockedUserIds() ?? collect();
        $technology = Technology::where('slug', $slug)->firstOrFail();

        $projects = $this->baseQuery()
            ->excludingBlocked($blockedIds)
            ->whereHas('technologies', fn (Builder $query) => $query->where('technologies.id', $technology->id));

        $projects = $this->orderByTrending($projects)->paginate(15);

        if (request()->ajax()) {
            return view('components.project-list', compact('projects'))->render();
        }

        return view('explore.index', compact('projects', 'technology'));
    }

    public function map()
    {
        return view('explore.index', ['projects' => collect()]);
    }

    public function search(Request $request)
    {
        $blockedIds = Auth::user()?->blockedUserIds() ?? collect();
        $query = trim((string) $request->get('q', ''));
        $like = "%{$query}%";

        $projectsQuery = $this->baseQuery()
            ->excludingBlocked($blockedIds)
            ->where(function (Builder $query) use ($like): void {
                $query->where('projects.title', 'like', $like)
                    ->orWhere('projects.content', 'like', $like)
                    ->orWhereHas('technologies', fn (Builder $query) => $query->where('name', 'like', $like));
            })
            ->orderByRaw('CASE WHEN projects.title LIKE ? THEN 0 ELSE 1 END', [$like]);

        $projects = $this->orderByTrending($projectsQuery)->paginate(15);

        if ($request->ajax()) {
            return view('components.project-list', compact('projects'))->render();
        }

        return view('explore.index', compact('projects'));
    }

    private function getProjects(string $type)
    {
        $blockedIds = Auth::user()?->blockedUserIds() ?? collect();

        $query = $this->baseQuery()->excludingBlocked($blockedIds);

        return match ($type) {
            'trending' => $this->orderByTrending($query)->paginate(15),
            'recent' => $query->orderByDesc('projects.created_at')->paginate(15),
            default => $query->orderByDesc('projects.created_at')->paginate(15),
        };
    }

    private function baseQuery(): Builder
    {
        $userId = Auth::id();

        return Project::with([
            'user.profile',
            'media',
            'technologies',
            'likes' => fn ($query) => $query->where('user_id', $userId),
            'bookmarks' => fn ($query) => $query->where('user_id', $userId),
            'skillEndorsements',
        ])->discoverable();
    }

    private function orderByTrending(Builder $query): Builder
    {
        return $query
            ->withCount('skillEndorsements')
            ->orderByRaw('((projects.likes_count + projects.comments_count * 2 + skill_endorsements_count * 3) / POW(TIMESTAMPDIFF(HOUR, projects.created_at, NOW()) + 2, 1.5)) DESC')
            ->orderByDesc('projects.created_at');
    }
}
