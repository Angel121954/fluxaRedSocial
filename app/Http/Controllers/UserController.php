<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Conversation;

class UserController extends Controller
{
    public function search(Request $request)
    {
        $query = $request->query('q', '');

        if (strlen($query) < 2) {
            return response()->json([]);
        }

        if (!auth()->check()) {
            return response()->json(['error' => 'No autenticado'], 401);
        }

        $currentUserId = auth()->id();

        $existingConvUserIds = Conversation::where('user_a_id', $currentUserId)
            ->orWhere('user_b_id', $currentUserId)
            ->select('user_a_id', 'user_b_id')
            ->get()
            ->map(function ($conv) use ($currentUserId) {
                return $conv->user_a_id === $currentUserId ? $conv->user_b_id : $conv->user_a_id;
            })
            ->unique()
            ->toArray();

        $users = User::where('id', '!=', $currentUserId)
            ->where(function ($userQuery) use ($query) {
                $userQuery->where('name', 'like', "%{$query}%")
                    ->orWhere('username', 'like', "%{$query}%");
            })
            ->whereHas('profile', function ($q) {
                $q->where('visibility', 'public')
                    ->where('accept_messages', true);
            })
            ->with('profile');

        if (!empty($existingConvUserIds)) {
            $users->whereNotIn('id', $existingConvUserIds);
        }

        $users = $users->limit(20)->get();

        $results = $users->map(function ($user) {
            return [
                'id' => $user->id,
                'username' => $user->username,
                'name' => $user->profile->name ?? $user->name,
                'avatar_url' => $user->avatar_url,
            ];
        });

        return response()->json($results);
    }
}