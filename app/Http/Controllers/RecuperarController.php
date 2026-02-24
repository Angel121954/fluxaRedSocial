<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
//use App\Models\;

class UserController extends Controller
{
    public function index()
    {
        return view('recuperar');
    }
}
