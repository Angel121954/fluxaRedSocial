<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateNotificationPreferenceRequest;

class NotificationPreferenceController extends Controller
{
    public function index()
    {
        return view('settings.notification-preference');
    }

    public function update(UpdateNotificationPreferenceRequest $request)
    {
        $user = $request->user();

        $user->notificationPreferences->update([
            'email_enabled' => $request->boolean('email_enabled'),
            'push_enabled' => $request->boolean('push_enabled'),
            'notify_comments' => $request->boolean('notify_comments'),
            'notify_followers' => $request->boolean('notify_followers'),
            'notify_mentions' => $request->boolean('notify_mentions'),
            'weekly_summary' => $request->boolean('weekly_summary'),
        ]);

        return redirect()->back()->with('success', 'Preferencias de notificaciones actualizadas');
    }
}
