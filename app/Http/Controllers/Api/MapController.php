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
        $blockedIds = Auth::check()
            ? Auth::user()->blockedUsers()->pluck('blocked_id')
            : collect();

        $users = User::whereHas('profile', fn ($q) => $q
            ->whereNotNull('latitude')
            ->whereNotNull('longitude'),
        )
            ->whereNotIn('id', $blockedIds)
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
            'url' => route('profile.index', $user->username ?? $user->id),
        ]));
    }
}
