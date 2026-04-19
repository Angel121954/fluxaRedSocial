<?php

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;
use App\Models\Profile;
use Illuminate\Support\Facades\Auth;

class AboutFluxaController extends Controller
{
    public function index()
    {
        return view('public.about-fluxa');
    }
}
