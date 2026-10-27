<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function search(Request $request)
    {
        $query = $request->get('q', '');

        if (strlen($query) < 2) {
            return response()->json(['users' => [], 'projects' => []]);
        }

        $users = User::whereHas('profile', fn ($q) => $q->where('visibility', 'public'))
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                    ->orWhere('username', 'like', "%{$query}%");
            })
            ->with('profile')
            ->limit(5)
            ->get()
            ->map(fn ($user) => [
                'id' => $user->id,
                'name' => $user->name,
                'username' => $user->username,
                'avatar' => $user->profile->avatar ?? 'https://api.dicebear.com/7.x/initials/svg?seed='.strtolower($user->username).'&backgroundColor=12b3b6',
            ]);

        $projects = Project::publicRoot()
            ->where('title', 'like', "%{$query}%")
            ->with('media')
            ->limit(5)
            ->get()
            ->map(fn ($project) => [
                'id' => $project->id,
                'name' => $project->title,
                'thumbnail' => $project->media->first()?->media_url ?? null,
            ]);

        return response()->json([
            'users' => $users,
            'projects' => $projects,
        ]);
    }
}
