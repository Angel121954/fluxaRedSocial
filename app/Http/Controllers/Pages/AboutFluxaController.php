<?php

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;
use App\Models\Profile;
use Illuminate\Support\Facades\Auth;

class AboutFluxaController extends Controller
{
    public function index()
    {
        $profile = Profile::where('user_id', Auth::user()->id)->first();

        return view('pages.about-fluxa', compact('profile'));
    }
}
