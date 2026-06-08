<?php

declare(strict_types=1);

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;

class TermsController extends Controller
{
    public function index()
    {
        return view('public.terms');
    }
}
