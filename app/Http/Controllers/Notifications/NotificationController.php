<?php

namespace App\Http\Controllers\Notifications;

use App\Http\Controllers\Controller;
use App\Models\Profile;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        $user = Auth::user()->id;
        $profile = Profile::where('user_id', $user)->first();

        return view('notifications.index', compact('profile'));
    }
}
