<?php

declare(strict_types=1);

use App\Http\Controllers\Api\LocationController;
use App\Http\Controllers\Api\SearchController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/search', [SearchController::class, 'search']);

Route::get('/locations/countries', [LocationController::class, 'countries']);
Route::get('/locations/{country}/cities', [LocationController::class, 'cities']);
