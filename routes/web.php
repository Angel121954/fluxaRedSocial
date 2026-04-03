<?php

use App\Http\Controllers\AboutFluxaController;
use App\Http\Controllers\Auth\GuestController;
use App\Http\Controllers\Auth\SocialAuthController;
use App\Http\Controllers\Explore\ExploreController;
use App\Http\Controllers\Notifications\NotificationController;
use App\Http\Controllers\OnboardingController;
use App\Http\Controllers\Profile\AccountController;
use App\Http\Controllers\Profile\ConfigurationController;
use App\Http\Controllers\Profile\EducationController;
use App\Http\Controllers\Profile\NotificationPreferenceController;
use App\Http\Controllers\Profile\PrivacyController;
use App\Http\Controllers\Profile\ProfileController;
use App\Http\Controllers\Profile\SecurityController;
use App\Http\Controllers\Profile\WorkExperienceController;
use App\Http\Controllers\Projects\ProjectController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Redirección inicial
|--------------------------------------------------------------------------
*/

Route::redirect('/', '/login');

/*
|--------------------------------------------------------------------------
| Invitado
|--------------------------------------------------------------------------
*/
Route::get('/guest-login', [GuestController::class, 'loginAsGuest'])
    ->name('auth.guest');

Route::post('/guest/destroy', [GuestController::class, 'destroyGuest'])
    ->name('guest.destroy')
    ->middleware('auth');

/*
|--------------------------------------------------------------------------
| Autenticación social
|--------------------------------------------------------------------------
*/
Route::get('/auth/{provider}', [SocialAuthController::class, 'redirect'])
    ->name('social.redirect');

Route::get('/auth/{provider}/callback', [SocialAuthController::class, 'callback'])
    ->name('social.callback');

/*
|--------------------------------------------------------------------------
| Onboarding (solo auth)
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
| CV
|--------------------------------------------------------------------------
*/
Route::get('/cv/preview-interno', [ProfileController::class, 'previewInterno'])
    ->name('cv.preview')
    ->middleware('auth');

Route::get('/cv/descargar', [ProfileController::class, 'descargarCV'])
    ->name('cv.descargar')
    ->middleware('auth');

/*
|--------------------------------------------------------------------------
| Rutas protegidas (auth + onboarding)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'prevent-back-history', 'onboarding'])->group(function () {

    Route::view('/dashboard', 'dashboard')
        ->name('dashboard');

    Route::get('/explore', [ExploreController::class, 'index'])
        ->name('explore.index');

    Route::get('/explore/trending', [ExploreController::class, 'trending'])
        ->name('explore.trending');

    Route::get('/explore/recent', [ExploreController::class, 'recent'])
        ->name('explore.recent');

    Route::get('/explore/following', [ExploreController::class, 'following'])
        ->name('explore.following');

    Route::get('/explore/topic/{slug}', [ExploreController::class, 'topic'])
        ->name('explore.topic');

    Route::get('/about-fluxa', [AboutFluxaController::class, 'index'])
        ->name('about-fluxa');

    /*
    |--------------------------------------------------------------------------
    | Solo usuarios reales (verificados y no guest)
    |--------------------------------------------------------------------------
    */
    Route::middleware(['verified', 'restrict.guest'])->group(function () {

        /*
        |--------------------------------------------------------------------------
        | Perfil
        |--------------------------------------------------------------------------
        */
        Route::get('/profile', [ProfileController::class, 'index'])
            ->name('profile.index');

        Route::post('/profile/avatar', [ProfileController::class, 'updateAvatar'])
            ->name('profile.avatar');

        Route::delete('/profile/avatar', [ProfileController::class, 'destroyAvatar'])
            ->name('profile.avatar.destroy');

        /*
        |--------------------------------------------------------------------------
        | Configuración y cuenta
        |--------------------------------------------------------------------------
        */
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

        /*
        |--------------------------------------------------------------------------
        | Seguridad y privacidad
        |--------------------------------------------------------------------------
        */
        Route::get('/security', [SecurityController::class, 'index'])
            ->name('security.index');

        Route::get('/privacy', [PrivacyController::class, 'index'])
            ->name('privacy.index');

        Route::patch('/privacy', [PrivacyController::class, 'update'])
            ->name('privacy.update');

        /*
        |--------------------------------------------------------------------------
        | Notificaciones
        |--------------------------------------------------------------------------
        */
        Route::get('/notifications', [NotificationController::class, 'index'])
            ->name('notifications.index');

        Route::get('/notification-preference', [NotificationPreferenceController::class, 'index'])
            ->name('notification-preference.index');

        Route::patch('/notification-preference', [NotificationPreferenceController::class, 'update'])
            ->name('notification-preference.update');

        /*
        |--------------------------------------------------------------------------
        | Recursos del perfil
        |--------------------------------------------------------------------------
        */
        Route::resource('work-experiences', WorkExperienceController::class);
        Route::resource('projects', ProjectController::class);
        Route::resource('educations', EducationController::class);

        Route::post('/projects/{project}/like', [ProjectController::class, 'like'])
            ->name('projects.like');

        Route::post('/projects/{project}/bookmark', [ProjectController::class, 'bookmark'])
            ->name('projects.bookmark');

        Route::post('/projects/{project}/report', [ProjectController::class, 'report'])
            ->name('projects.report');

        /*
        |--------------------------------------------------------------------------
        | Tecnologías (AJAX)
        |--------------------------------------------------------------------------
        */
        Route::get(
            '/technologies',
            fn () => \App\Models\Technology::orderBy('name')
                ->get(['id', 'name', 'icon'])
        )->name('technologies.index');
    });
});

require __DIR__.'/auth.php';
