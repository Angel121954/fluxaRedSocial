<?php

namespace App\Http\Controllers;

use App\Models\UserReport;
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
            ->whereHas('profile', function ($q) use ($query) {
                $q->where('visibility', 'public')
                    ->where('accept_messages', true)
                    ->where(function ($q2) use ($query) {
                        $q2->where('name', 'like', "%{$query}%")
                            ->orWhere('username', 'like', "%{$query}%");
                    });
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

    public function report(Request $request, User $user)
    {
        if ($user->id === auth()->id()) {
            return response()->json(['message' => 'No puedes reportarte a ti mismo'], 400);
        }

        $validated = $request->validate([
            'reason' => 'required|string|min:10',
        ]);

        $existing = UserReport::where('reporter_id', auth()->id())
            ->where('reported_id', $user->id)
            ->first();

        if ($existing) {
            return response()->json([
                'message' => 'Ya reportaste a este usuario. Lo estaremos revisando.',
            ], 409);
        }

        UserReport::create([
            'reporter_id' => auth()->id(),
            'reported_id' => $user->id,
            'reason' => $validated['reason'],
        ]);

        return response()->json([
            'message' => 'Reporte enviado. Gracias por ayudar a mantener la comunidad segura.',
        ]);
    }
}