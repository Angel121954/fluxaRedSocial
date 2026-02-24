<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\FlareClient\View;

class AccountController extends Controller
{
    public function index()
    {
        return view('profile.account');
    }
}
