<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\Profile;

class PrivacyController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $profile = Profile::where('user_id', $user->id)->first();
        return view('profile.privacy', compact('profile'));
    }
    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'visibility'      => ['nullable', 'in:on,1,private'],
            'accept_messages' => ['nullable', 'in:on,1'],
            'show_email'      => ['nullable', 'in:on,1'],
        ]);

        Profile::where('user_id', $user->id)->update([
            'visibility' => $request->has('visibility') ? 'private' : 'public',
            'accept_messages' => $request->boolean('accept_messages'),
            'show_email'      => $request->boolean('show_email'),
        ]);

        return redirect()->back()->with('success', 'Preferencias de privacidad actualizadas');
    }
}
