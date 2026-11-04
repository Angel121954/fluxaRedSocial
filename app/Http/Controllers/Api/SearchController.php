<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SearchController extends Controller
{
    public function search(Request $request)
    {
        $query = trim((string) $request->get('q', ''));

        if (mb_strlen($query) < 2) {
            return response()->json(['users' => [], 'projects' => []]);
        }

        $user = Auth::user();
        $blockedIds = $user?->blockedUserIds() ?? collect();
        $like = "%{$query}%";

        $users = User::query()
            ->visible()
            ->where('role', '!=', 'guest')
            ->whereHas('profile', fn (Builder $query) => $query->where('visibility', 'public'))
            ->when($blockedIds->isNotEmpty(), fn (Builder $query) => $query->whereNotIn('id', $blockedIds))
            ->where(function (Builder $query) use ($like): void {
                $query->where('name', 'like', $like)
                    ->orWhere('username', 'like', $like);
            })
            ->with('profile')
            ->limit(5)
            ->get()
            ->map(fn (User $user) => [
                'id' => $user->id,
                'name' => $user->name,
                'username' => $user->username,
                'avatar' => $user->profile?->avatar
                    ?? 'https://api.dicebear.com/7.x/initials/svg?seed='.strtolower($user->username).'&backgroundColor=12b3b6',
            ]);

        $projects = Project::query()
            ->discoverable()
            ->excludingBlocked($blockedIds)
            ->where(function (Builder $query) use ($like): void {
                $query->where('title', 'like', $like)
                    ->orWhere('content', 'like', $like)
                    ->orWhereHas('technologies', fn (Builder $query) => $query->where('name', 'like', $like));
            })
            ->with('media')
            ->limit(5)
            ->get()
            ->map(fn (Project $project) => [
                'id' => $project->id,
                'slug' => $project->slug,
                'name' => $project->title,
                'thumbnail' => $project->media->firstWhere('type', 'image')?->media_url,
            ]);

        return response()->json([
            'users' => $users,
            'projects' => $projects,
        ]);
    }
}
