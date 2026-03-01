<?php

namespace App\Http\Controllers;

use App\Models\Profile;
use Illuminate\Http\Request;
use Spatie\FlareClient\View;
use Illuminate\Support\Facades\Auth;

class AboutFluxaController extends Controller
{
    public function index()
    {
        $profile = Profile::where('user_id', Auth::user()->id)->first();
        return view('about-fluxa', compact('profile'));
    }
}
