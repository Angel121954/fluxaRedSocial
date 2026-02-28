<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Profile;

class ExploreController extends Controller
{
    public function index()
    {
        $user = Auth::user()->id;
        $profile = Profile::where('user_id', $user)->first();
        return view('explore', compact('profile'));
    }
}
