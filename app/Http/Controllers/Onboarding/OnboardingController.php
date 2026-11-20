<?php

declare(strict_types=1);

namespace App\Http\Controllers\Onboarding;

use App\Http\Controllers\Controller;
use App\Http\Requests\Onboarding\StoreRoleRequest;
use App\Models\Technology;
use App\Models\User;
use App\Services\AnalyticsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OnboardingController extends Controller
{
    public function technologies()
    {
        /** @var User $user */
        $user = Auth::user();

        AnalyticsService::track('onboarding_step_viewed', $user, ['step' => 'technologies']);

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
        /** @var User $user */
        $user = Auth::user();
        $technologies = $request->technologies ?? [];

        $user->technologies()->sync($technologies);

        AnalyticsService::track('onboarding_step_completed', $user, [
            'step' => 'technologies',
            'technologies_count' => count($technologies),
        ]);

        return redirect()->route('onboarding.role');
    }

    public function role()
    {
        /** @var User $user */
        $user = Auth::user();

        AnalyticsService::track('onboarding_step_viewed', $user, ['step' => 'role']);

        return view('onboarding.role');
    }

    public function saveRole(StoreRoleRequest $request)
    {
        /** @var User $user */
        $user = Auth::user();

        if (! $user) {
            return redirect()->route('login');
        }

        $user->profile()->updateOrCreate([], [
            'specialty' => $request->role,
            'intent' => $request->intent,
        ]);

        $user->update([
            'onboarding_completed' => true,
            'account_type' => $user->account_type ?? 'developer',
        ]);

        AnalyticsService::track('onboarding_step_completed', $user, [
            'step' => 'role',
            'role' => $request->role,
            'intent' => $request->intent,
        ]);

        AnalyticsService::track('onboarding_completed', $user, [
            'role' => $request->role,
            'intent' => $request->intent,
            'technologies_count' => $user->technologies()->count(),
        ]);

        return redirect()->route('onboarding.firstProject');
    }

    public function firstProject()
    {
        /** @var User $user */
        $user = Auth::user();

        if (! $user->onboarding_completed) {
            return redirect()->route('onboarding.technologies');
        }

        AnalyticsService::track('onboarding_step_viewed', $user, ['step' => 'first_project']);

        return view('onboarding.first-project', [
            'canImportGithub' => $user->hasVerifiedEmail(),
            'isVerified' => $user->hasVerifiedEmail(),
        ]);
    }
}
