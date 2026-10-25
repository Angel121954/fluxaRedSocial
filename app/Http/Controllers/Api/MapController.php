<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class MapController extends Controller
{
    public function users()
    {
        $currentUser = Auth::user();

        $blockedIds = $currentUser->blockedUsers()->pluck('users.id')
            ->merge($currentUser->blockedBy()->pluck('users.id'));

        $users = User::whereHas('profile', fn ($q) => $q
            ->where('visibility', 'public')
            ->whereNotNull('latitude')
            ->whereNotNull('longitude'),
        )
            ->whereNotIn('id', $blockedIds)
            ->where('role', '!=', 'guest')
            ->where('status', '!=', 'banned')
            ->with('profile:id,user_id,avatar,latitude,longitude')
            ->limit(500)
            ->get();

        return response()->json($users->map(fn ($user) => [
            'id' => $user->id,
            'name' => $user->name,
            'username' => $user->username,
            'avatar' => $user->avatar_url,
            'latitude' => (float) $user->profile->latitude,
            'longitude' => (float) $user->profile->longitude,
            'url' => route('profile.show', $user->username ?? $user->id),
        ]));
    }
}
