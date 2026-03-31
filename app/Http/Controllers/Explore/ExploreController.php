<?php

namespace App\Http\Controllers\Explore;

use App\Http\Controllers\Controller;
use App\Models\Profile;
use Illuminate\Support\Facades\Auth;

class ExploreController extends Controller
{
    public function index()
    {
        $user = Auth::user()->id;
        $profile = Profile::where('user_id', $user)->first();

        return view('explore.index', compact('profile'));
    }
}
