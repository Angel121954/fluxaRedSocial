<?php

namespace App\Http\Controllers\Technology;

use App\Http\Controllers\Controller;
use App\Models\Technology;
use Illuminate\Http\JsonResponse;

class TechnologyController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(
            Technology::orderBy('name')->get(['id', 'name', 'icon'])
        );
    }
}
