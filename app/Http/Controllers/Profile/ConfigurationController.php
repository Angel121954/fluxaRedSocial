<?php

declare(strict_types=1);

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use App\Http\Requests\Profile\UpdateConfigurationRequest;
use App\Services\BadgeService;

class ConfigurationController extends Controller
{
    public function __construct(
        protected BadgeService $badgeService,
    ) {}

    public function index()
    {
        return view('settings.configuration');
    }

    public function update(UpdateConfigurationRequest $request)
    {
        $user = $request->user();

        $user->update([
            'name' => $request->name,
            'username' => $request->username,
        ]);

        $user->profile->update([
            'bio' => $request->bio,
            'website_url' => $request->website_url,
            'github_url' => $request->github_url,
            'twitter_url' => $request->twitter_url,
            'linkedin_url' => $request->linkedin_url,
            'birth_date' => $request->birth_date,
            'gender' => $request->gender,
        ]);

        $this->badgeService->scanUser($user);

        return redirect()->back()->with('success', 'Perfil actualizado correctamente');
    }
}
