<?php

declare(strict_types=1);

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\SkillEndorsement;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class LandingController extends Controller
{
    public function __invoke(): View|RedirectResponse
    {
        if (Auth::check()) {
            return Auth::user()->role === 'guest'
                ? redirect()->route('explore.index')
                : redirect()->route('feed.index');
        }

        return view('public.landing', [
            'stats' => $this->stats(),
            'projects' => $this->recentProjects(),
        ]);
    }

    private function stats(): array
    {
        return Cache::remember('landing.stats', 3600, function () {
            return [
                'developers' => User::visible()
                    ->where('role', '!=', 'guest')
                    ->whereNotNull('email_verified_at')
                    ->count(),
                'projects' => Project::discoverable()->count(),
                'endorsements' => SkillEndorsement::count(),
            ];
        });
    }

    private function recentProjects()
    {
        return Project::with(['user.profile', 'technologies', 'media'])
            ->withCount('likes')
            ->discoverable()
            ->latest()
            ->take(6)
            ->get();
    }
}
