<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateConfigurationRequest;

class ConfigurationController extends Controller
{
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
            'location' => $request->location,
            'website_url' => $request->website_url,
            'github_url' => $request->github_url,
            'twitter_url' => $request->twitter_url,
            'linkedin_url' => $request->linkedin_url,
            'birth_date' => $request->birth_date,
            'gender' => $request->gender,
        ]);

        return redirect()->back()->with('success', 'Perfil actualizado correctamente');
    }
}
