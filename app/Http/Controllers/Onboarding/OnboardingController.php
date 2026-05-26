<?php

namespace App\Http\Controllers\Onboarding;

use App\Http\Controllers\Controller;
use App\Models\Technology;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OnboardingController extends Controller
{
    public function accountType()
    {
        return view('onboarding.account-type');
    }

    public function saveAccountType(Request $request)
    {
        $request->validate([
            'account_type' => 'required|in:developer,company',
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();
        $user->update(['account_type' => $request->account_type]);

        return redirect()->route('onboarding.technologies');
    }

    public function technologies()
    {
        $technologies = Technology::orderBy('name')->get();

        $featuredSlugs = [
            'javascript', 'typescript', 'python', 'php', 'java', 'go', 'rust',
            'react', 'nextjs', 'vuejs', 'angularjs', 'svelte', 'tailwindcss',
            'nodejs', 'laravel', 'django', 'spring',
            'reactnative', 'flutter', 'swift', 'kotlin',
            'mysql', 'postgresql', 'mongodb', 'redis', 'firebase',
            'amazonwebservices', 'docker', 'kubernetes', 'git', 'linux',
            'figma', 'graphql', 'tensorflow',
        ];

        return view('onboarding.technologies', compact('technologies', 'featuredSlugs'));
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
        if (! $user) {
            return redirect()->route('login');
        }
        $user->update(['role' => $request->role]);

        return redirect()->route('onboarding.bio');
    }

    public function bio()
    {
        return view('onboarding.bio');
    }

    public function saveBio(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if (! $user) {
            return redirect()->route('login');
        }

        $request->validate([
            'bio' => 'nullable|string|max:400',
        ]);

        if ($request->filled('bio')) {
            $user->profile()->update(['bio' => strip_tags($request->bio)]);
        }

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

        if (! $user) {
            return redirect()->route('login');
        }

        $followIds = $request->input('follow', []);
        foreach ($followIds as $id) {
            if (is_numeric($id) && $user->id != $id) {
                $user->follows()->syncWithoutDetaching([(int) $id]);
            }
        }

        $user->update(['onboarding_completed' => true]);

        return redirect()->route('explore.index');
    }
}
