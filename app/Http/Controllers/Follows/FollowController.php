<?php

namespace App\Http\Controllers\Follows;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\CreatesNotifications;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FollowController extends Controller
{
    public function toggle(Request $request, User $user): JsonResponse
    {
        $currentUser = Auth::user();

        if ($currentUser->id === $user->id) {
            return response()->json(['message' => 'No puedes seguirte a ti mismo'], 400);
        }

        $isFollowing = $currentUser->follows()->where('followed_id', $user->id)->exists();

        if ($isFollowing) {
            $currentUser->follows()->detach($user->id);
            $following = false;
        } else {
            $currentUser->follows()->attach($user->id);
            $following = true;

            CreatesNotifications::notifyNewFollower(
                $user->id,
                $currentUser->id,
                $currentUser->name
            );
        }

        return response()->json([
            'following' => $following,
            'followers_count' => $user->followers()->count(),
            'following_count' => $user->follows()->count(),
        ]);
    }

    public function followers(User $user): JsonResponse
    {
        $followers = $user->followers()
            ->select('users.id', 'users.name', 'users.username')
            ->get()
            ->map(fn($u) => [
                'id' => $u->id,
                'name' => $u->name,
                'username' => $u->username,
                'avatar_url' => $u->avatar_url,
            ]);

        return response()->json(['followers' => $followers]);
    }

    public function following(User $user): JsonResponse
    {
        $following = $user->follows()
            ->select('users.id', 'users.name', 'users.username')
            ->get()
            ->map(fn($u) => [
                'id' => $u->id,
                'name' => $u->name,
                'username' => $u->username,
                'avatar_url' => $u->avatar_url,
            ]);

        return response()->json(['following' => $following]);
    }
}