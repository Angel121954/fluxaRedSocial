<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Profile;
use App\Models\NotificationPreference;

class NotificationPreferenceController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $profile = Profile::where('user_id', $user->id)->first();
        return view('profile.notification-preference', compact('profile'));
    }
    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'email_enabled'      => ['nullable', 'in:on,1'],
            'push_enabled'      => ['nullable', 'in:on,1'],
            'notify_comments'      => ['nullable', 'in:on,1'],
            'notify_mentions'      => ['nullable', 'in:on,1'],
            'weekly_summary'      => ['nullable', 'in:on,1'],
        ]);

        NotificationPreference::where('user_id', $user->id)->update([
            'email_enabled' => $request->boolean('email_enabled'),
            'push_enabled' => $request->boolean('push_enabled'),
            'notify_comments'      => $request->boolean('notify_comments'),
            'notify_mentions'      => $request->boolean('notify_mentions'),
            'weekly_summary'      => $request->boolean('weekly_summary'),
        ]);

        return redirect()->back()->with('success', 'Preferencias de notificaciones actualizadas');
    }
}
