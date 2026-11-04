<?php

declare(strict_types=1);

use App\Http\Controllers\Api\GiphyController;
use App\Http\Controllers\Api\LocationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/locations/countries', [LocationController::class, 'countries']);
Route::get('/locations/{country}/cities', [LocationController::class, 'cities']);

Route::get('/giphy/search', [GiphyController::class, 'search']);
Route::get('/giphy/trending', [GiphyController::class, 'trending']);
