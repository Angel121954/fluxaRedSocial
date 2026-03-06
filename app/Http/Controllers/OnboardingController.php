<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Technology;

class OnboardingController extends Controller
{
    public function technologies()
    {
        $technologies = Technology::orderBy('name')->get();
        return view('onboarding.technologies', compact('technologies'));
    }

    public function saveTechnologies(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $user->technologies()->sync($request->technologies ?? []);
        return redirect()->route('onboarding.role');
    }

    public function role()
    {
        return view('onboarding.role');
    }

    public function saveRole(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }
        $user->update(['role' => $request->role]);
        return redirect()->route('onboarding.suggestions');
    }

    public function suggestions()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $suggested = User::where('users.id', '!=', $user->id)
            ->join('profiles', 'profiles.user_id', '=', 'users.id')
            ->select('users.id', 'users.name', 'users.username', 'users.role', 'profiles.avatar')
            ->inRandomOrder()
            ->take(6)
            ->get();

        return view('onboarding.suggestions', compact('suggested'));
    }

    public function saveSuggestions(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $user->update(['onboarding_completed' => true]);
        return redirect()->route('explore.index');
    }
}
