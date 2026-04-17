<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;

class SecurityController extends Controller
{
    public function index()
    {
        return view('profile.security');
    }
}
