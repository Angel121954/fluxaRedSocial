<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Profile;

class ConfigurationController extends Controller
{
    public function index()
    {
        $user = Auth::user()->id;
        $profile = Profile::where('user_id', $user)->first();
        return view('configuration', compact('profile'));
    }
}
