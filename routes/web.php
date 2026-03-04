<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Auth\SocialAuthController;
use App\Http\Controllers\Explore\ExploreController;
use App\Http\Controllers\Notifications\NotificationController;
use App\Http\Controllers\AboutFluxaController;

use App\Http\Controllers\Profile\NotificationPreferenceController;
use App\Http\Controllers\Profile\ProfileController;
use App\Http\Controllers\Profile\AccountController;
use App\Http\Controllers\Profile\SecurityController;
use App\Http\Controllers\Profile\ConfigurationController;
use App\Http\Controllers\Profile\PrivacyController;

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

    Route::post('/profile/avatar', [ProfileController::class, 'updateAvatar'])
        ->name('profile.avatar');

    Route::delete('/profile/avatar', [ProfileController::class, 'destroyAvatar'])
        ->name('profile.avatar.destroy');

    Route::get('/configuration', [ConfigurationController::class, 'index'])
        ->name('configuration.index');

    Route::patch('/configuration', [ConfigurationController::class, 'update'])
        ->name('configuration.edit');

    Route::get('/account', [AccountController::class, 'index'])
        ->name('account.index');

    Route::post('/account', [AccountController::class, 'update'])
        ->name('account.edit');

    Route::patch('/account/deactivate', [AccountController::class, 'deactivate'])
        ->name('account.deactivate');

    Route::delete('/account', [AccountController::class, 'destroy'])
        ->name('account.destroy');

    Route::get('/security', [SecurityController::class, 'index'])
        ->name('security.index');

    Route::get('/privacy', [PrivacyController::class, 'index'])
        ->name('privacy.index');

    Route::patch('/privacy', [PrivacyController::class, 'update'])
        ->name('privacy.update');

    Route::get('/explore', [ExploreController::class, 'index'])
        ->name('explore.index');

    Route::get('/notifications', [NotificationController::class, 'index'])
        ->name('notifications.index');

    Route::get('/notification-preference', [NotificationPreferenceController::class, 'index'])
        ->name('notification-preference.index');

    Route::patch('/notification-preference', [NotificationPreferenceController::class, 'update'])
        ->name('notification-preference.update');

    Route::get('/about-fluxa', [AboutFluxaController::class, 'index'])
        ->name('about-fluxa');
});

require __DIR__ . '/auth.php';
