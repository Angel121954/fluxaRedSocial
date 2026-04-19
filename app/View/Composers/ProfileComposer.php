<?php

namespace App\View\Composers;

use App\Models\Profile;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ProfileComposer
{
    public function compose(View $view): void
    {
        $profile = null;

        if (Auth::check()) {
            $profile = Profile::where('user_id', Auth::id())->first();
        }

        $view->with('profile', $profile);
    }
}