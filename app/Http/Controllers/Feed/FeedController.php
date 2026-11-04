<?php

declare(strict_types=1);

namespace App\Http\Controllers\Feed;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

class FeedController extends Controller
{
    public function index(Request $request)
    {
        $tab = $this->resolveTab($request);
        $isFallback = $tab === 'following' && Auth::user()->follows()->doesntExist();

        $projects = $tab === 'following' && ! $isFallback
            ? $this->followingProjects()
            : $this->forYouProjects();

        if ($request->ajax()) {
            return view('components.project-list', compact('projects'))->render();
        }

        return view('feed.index', compact('projects', 'tab', 'isFallback'));
    }

    private function resolveTab(Request $request): string
    {
        return $request->query('tab') === 'following' ? 'following' : 'for-you';
    }

    private function forYouProjects(): LengthAwarePaginator
    {
        $user = Auth::user();
        $blockedIds = $user->blockedUserIds();
        $technologyIds = $user->technologies()->pluck('technologies.id');
        $specialty = $user->profile?->specialty;

        $query = $this->baseQuery()
            ->excludingBlocked($blockedIds)
            ->where('projects.user_id', '!=', $user->id)
            ->withCount('skillEndorsements');

        if ($technologyIds->isNotEmpty()) {
            $query->withCount([
                'technologies as affinity_count' => fn ($query) => $query->whereIn('technologies.id', $technologyIds),
            ])->orderByDesc('affinity_count');
        }

        if ($specialty) {
            $query->orderByRaw(
                'CASE WHEN EXISTS (SELECT 1 FROM profiles WHERE profiles.user_id = projects.user_id AND profiles.specialty = ?) THEN 0 ELSE 1 END',
                [$specialty],
            );
        }

        return $query
            ->orderByRaw($this->engagementScore())
            ->orderByDesc('projects.created_at')
            ->paginate(15);
    }

    private function followingProjects(): LengthAwarePaginator
    {
        $user = Auth::user();
        $blockedIds = $user->blockedUserIds();
        $followingIds = $user->follows()->pluck('followed_id')->diff($blockedIds);

        if ($followingIds->isEmpty()) {
            return new LengthAwarePaginator([], 0, 15);
        }

        return $this->baseQuery()
            ->whereIn('projects.user_id', $followingIds)
            ->orderByDesc('projects.created_at')
            ->paginate(15);
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

    private function engagementScore(): string
    {
        return '((projects.likes_count + projects.comments_count * 2 + skill_endorsements_count * 3) / POW(TIMESTAMPDIFF(HOUR, projects.created_at, NOW()) + 2, 1.5)) DESC';
    }
}
