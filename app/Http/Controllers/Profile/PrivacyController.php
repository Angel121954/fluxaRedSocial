<?php

namespace App\Http\Controllers\Profile;

use App\Events\PrivacyUpdated;
use App\Http\Controllers\Controller;
use App\Http\Requests\UpdatePrivacyRequest;
use App\Models\Conversation;

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
            'show_favorites' => $request->boolean('show_favorites'),
        ]);

        $convIds = Conversation::where('user_a_id', $user->id)
            ->orWhere('user_b_id', $user->id)
            ->pluck('id')
            ->toArray();

        if (!empty($convIds)) {
            broadcast(new PrivacyUpdated(
                userId: $user->id,
                userName: $user->name,
                acceptMessages: $user->profile->accept_messages,
                conversationIds: $convIds
            ));
        }

        return redirect()->back()->with('success', 'Preferencias de privacidad actualizadas');
    }
}
