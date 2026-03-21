<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Auth\SocialAuthController;
use App\Http\Controllers\Auth\GuestController;
use App\Http\Controllers\Projects\ProjectController;
use App\Http\Controllers\Explore\ExploreController;
use App\Http\Controllers\Notifications\NotificationController;
use App\Http\Controllers\AboutFluxaController;
use App\Http\Controllers\OnboardingController;

use App\Http\Controllers\Profile\NotificationPreferenceController;
use App\Http\Controllers\Profile\ProfileController;
use App\Http\Controllers\Profile\AccountController;
use App\Http\Controllers\Profile\SecurityController;
use App\Http\Controllers\Profile\ConfigurationController;
use App\Http\Controllers\Profile\PrivacyController;
use App\Http\Controllers\Profile\WorkExperienceController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::redirect('/', '/login');

/*
|--------------------------------------------------------------------------
| Visitante (sin autenticación)
|--------------------------------------------------------------------------
*/
Route::get('/guest-login', [GuestController::class, 'loginAsGuest'])
    ->name('auth.guest');

Route::post('/guest/destroy', [GuestController::class, 'destroyGuest'])
    ->name('guest.destroy')
    ->middleware('auth');

/*
|--------------------------------------------------------------------------
| Onboarding (auth requerido, sin verificación de email ni onboarding check)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'prevent-back-history'])->group(function () {
    Route::get('/onboarding/technologies', [OnboardingController::class, 'technologies'])
        ->name('onboarding.technologies');
    Route::post('/onboarding/technologies', [OnboardingController::class, 'saveTechnologies'])
        ->name('onboarding.saveTechnologies');

    Route::get('/onboarding/role', [OnboardingController::class, 'role'])
        ->name('onboarding.role');
    Route::post('/onboarding/role', [OnboardingController::class, 'saveRole'])
        ->name('onboarding.saveRole');

    Route::get('/onboarding/suggestions', [OnboardingController::class, 'suggestions'])
        ->name('onboarding.suggestions');
    Route::post('/onboarding/suggestions', [OnboardingController::class, 'saveSuggestions'])
        ->name('onboarding.saveSuggestions');
});

/*
|--------------------------------------------------------------------------
| Social Authentication (Google, GitHub, Facebook)
|--------------------------------------------------------------------------
*/
Route::get('/auth/{provider}', [SocialAuthController::class, 'redirect'])
    ->name('social.redirect');

Route::get('/auth/{provider}/callback', [SocialAuthController::class, 'callback'])
    ->name('social.callback');

Route::get('/cv/preview-interno', [ProfileController::class, 'previewInterno'])
    ->name('cv.preview')
    ->middleware('auth');

Route::get('/cv/descargar', [ProfileController::class, 'descargarCV'])
    ->name('cv.descargar')
    ->middleware('auth');

/*
|--------------------------------------------------------------------------
| Rutas protegidas — accesibles también para visitantes (role: guest)
| Solo requieren auth + prevent-back-history + onboarding completado
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'prevent-back-history', 'onboarding'])->group(function () {

    Route::view('/dashboard', 'dashboard')
        ->name('dashboard');

    Route::get('/explore', [ExploreController::class, 'index'])
        ->name('explore.index');

    Route::get('/about-fluxa', [AboutFluxaController::class, 'index'])
        ->name('about-fluxa');

    /*
    |--------------------------------------------------------------------------
    | Rutas bloqueadas para visitantes — requieren cuenta real
    |--------------------------------------------------------------------------
    */
    Route::middleware(['verified', 'restrict.guest'])->group(function () {

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

        Route::get('/notifications', [NotificationController::class, 'index'])
            ->name('notifications.index');

        Route::get('/notification-preference', [NotificationPreferenceController::class, 'index'])
            ->name('notification-preference.index');

        Route::patch('/notification-preference', [NotificationPreferenceController::class, 'update'])
            ->name('notification-preference.update');

        Route::resource('work-experiences', WorkExperienceController::class);

        Route::resource('projects', ProjectController::class);

        Route::get(
            '/technologies',
            fn() =>
            \App\Models\Technology::orderBy('name')->get(['id', 'name', 'icon'])
        )->name('technologies.index');
    });
});

require __DIR__ . '/auth.php';
