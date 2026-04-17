<?php

namespace App\View\Composers;

use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ProfileComposer
{
    public function compose(View $view)
    {
        if (! Auth::check()) {
            return;
        }

        $user = Auth::user();
        $profile = $user->profile;

        $view->with(compact('user', 'profile'));
    }
}
