<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdatePrivacyRequest;

class PrivacyController extends Controller
{
    public function index()
    {
        return view('settings.privacy');
    }

    public function update(UpdatePrivacyRequest $request)
    {
        $user = $request->user();

        $user->profile->update([
            'visibility' => $request->has('visibility') ? 'private' : 'public',
            'accept_messages' => $request->boolean('accept_messages'),
            'show_email' => $request->boolean('show_email'),
            'show_bookmarks' => $request->boolean('show_bookmarks'),
        ]);

        return redirect()->back()->with('success', 'Preferencias de privacidad actualizadas');
    }
}
