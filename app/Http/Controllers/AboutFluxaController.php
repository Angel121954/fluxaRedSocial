<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\FlareClient\View;

class AboutFluxaController extends Controller
{
    public function index()
    {
        return view('about-fluxa');
    }
}
