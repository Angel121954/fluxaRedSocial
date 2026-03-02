<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ExploreController;
use App\Http\Controllers\NotificationsController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AboutFluxaController;
use App\Http\Controllers\ConfigurationController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\Auth\SocialAuthController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::redirect('/', '/login');

/*
|--------------------------------------------------------------------------
| Social Authentication (Google, GitHub, etc.)
|--------------------------------------------------------------------------
*/
Route::get('/auth/{provider}', [SocialAuthController::class, 'redirect'])
    ->name('social.redirect');

Route::get('/auth/{provider}/callback', [SocialAuthController::class, 'callback'])
    ->name('social.callback');


/*
|--------------------------------------------------------------------------
| Protected Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified', 'prevent-back-history'])->group(function () {

    Route::view('/dashboard', 'dashboard')
        ->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'index'])
        ->name('profile.index');

    Route::get('/configuration', [ConfigurationController::class, 'index'])
        ->name('configuration.index');

    Route::get('/account', [AccountController::class, 'index'])
        ->name('account.index');

    Route::post('/account', [AccountController::class, 'update'])
        ->name('account.edit');

    Route::patch('/account/deactivate', [AccountController::class, 'deactivate'])
        ->name('account.deactivate');

    Route::delete('/account', [AccountController::class, 'destroy'])
        ->name('account.destroy');

    Route::get('/explore', [ExploreController::class, 'index'])
        ->name('explore.index');

    Route::get('/notifications', [NotificationsController::class, 'index'])
        ->name('notifications.index');

    Route::get('/about-fluxa', [AboutFluxaController::class, 'index'])
        ->name('about-fluxa');
});

require __DIR__ . '/auth.php';
