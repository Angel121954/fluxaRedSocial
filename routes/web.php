<?php

use App\Http\Controllers\Auth\GuestController;
use App\Http\Controllers\Auth\SocialAuthController;
use App\Http\Controllers\Explore\ExploreController;
use App\Http\Controllers\Feed\FeedController;
use App\Http\Controllers\Notifications\NotificationController;
use App\Http\Controllers\Onboarding\OnboardingController;
use App\Http\Controllers\Pages\AboutFluxaController;
use App\Http\Controllers\Pages\TermsController;
use App\Http\Controllers\Pages\PrivacyPolicyController;
use App\Http\Controllers\Pages\ContactController;
use App\Http\Controllers\Profile\AccountController;
use App\Http\Controllers\Profile\ConfigurationController;
use App\Http\Controllers\Profile\CVSettingsController;
use App\Http\Controllers\Profile\EducationController;
use App\Http\Controllers\Profile\NotificationPreferenceController;
use App\Http\Controllers\Profile\PrivacyController;
use App\Http\Controllers\Profile\ProfileController;
use App\Http\Controllers\Profile\SecurityController;
use App\Http\Controllers\Profile\WorkExperienceController;
use App\Http\Controllers\Projects\ProjectController;
use App\Http\Controllers\Suggestions\SuggestionController;
use App\Http\Controllers\Technology\TechnologyController;
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

Route::get('/cv/download/{username?}', [ProfileController::class, 'downloadCV'])
    ->name('cv.download.public')
    ->middleware('auth');

/*
|--------------------------------------------------------------------------
| Rutas protegidas (auth + onboarding)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'prevent-back-history', 'onboarding'])->group(function () {

    Route::view('/dashboard', 'dashboard')
        ->name('dashboard');

    Route::get('/feed', [FeedController::class, 'index'])
        ->name('feed.index');

    Route::get('/feed/paginate', [FeedController::class, 'paginate'])
        ->name('feed.paginate');

    Route::get('/explore', [ExploreController::class, 'index'])
        ->name('explore.index');

    Route::get('/explore/search', [ExploreController::class, 'search'])
        ->name('explore.search');

    Route::get('/explore/trending', [ExploreController::class, 'trending'])
        ->name('explore.trending');

    Route::get('/explore/recent', [ExploreController::class, 'recent'])
        ->name('explore.recent');

    Route::get('/explore/topic/{slug}', [ExploreController::class, 'topic'])
        ->name('explore.topic');

    Route::get('/about-fluxa', [AboutFluxaController::class, 'index'])
        ->name('about-fluxa');

    Route::get('/terms', [TermsController::class, 'index'])
        ->name('terms');

    Route::get('/privacy-policy', [PrivacyPolicyController::class, 'index'])
        ->name('privacy.policy');

    Route::get('/contact', [ContactController::class, 'index'])
        ->name('contact.index');

    Route::post('/contact', [ContactController::class, 'store'])
        ->name('contact.store');

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

        Route::get('/profile/{username}', [ProfileController::class, 'show'])
            ->name('profile.show');

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
        | Sugerencias
        |--------------------------------------------------------------------------
        */
        Route::get('suggestions/create', [SuggestionController::class, 'create'])
            ->name('suggestions.create');

        Route::post('suggestions', [SuggestionController::class, 'store'])
            ->name('suggestions.store');

        /*
        |--------------------------------------------------------------------------
        | Sugerencias (admin)
        |--------------------------------------------------------------------------
        */
        Route::get('admin/suggestions', [SuggestionController::class, 'index'])
            ->name('admin.suggestions.index')
            ->middleware('admin');

        Route::get('admin/suggestions/{suggestion}', [SuggestionController::class, 'show'])
            ->name('admin.suggestions.show')
            ->middleware('admin');

        Route::patch('admin/suggestions/{suggestion}/approve', [SuggestionController::class, 'approve'])
            ->name('admin.suggestions.approve')
            ->middleware('admin');

        Route::patch('admin/suggestions/{suggestion}/markRead', [SuggestionController::class, 'markRead'])
            ->name('admin.suggestions.markRead')
            ->middleware('admin');

        Route::delete('admin/suggestions/{suggestion}', [SuggestionController::class, 'destroy'])
            ->name('admin.suggestions.destroy')
            ->middleware('admin');

        /*
        |--------------------------------------------------------------------------
        | Recursos del perfil
        |--------------------------------------------------------------------------
        */
        Route::resource('work-experiences', WorkExperienceController::class);
        Route::get('/settings/cv', [CvSettingsController::class, 'edit'])->name('cv.edit');
        Route::put('/settings/cv', [CvSettingsController::class, 'update'])->name('cv.update');
        Route::get('/settings/cv/restore', [CvSettingsController::class, 'restore'])->name('cv.restore');
        Route::get('/settings/cv/download', [CvSettingsController::class, 'download'])->name('cv.download');
        Route::resource('projects', ProjectController::class);
        Route::resource('educations', EducationController::class);

        Route::post('/projects/{project}/like', [ProjectController::class, 'like'])
            ->name('projects.like');

        Route::post('/projects/{project}/bookmark', [ProjectController::class, 'bookmark'])
            ->name('projects.bookmark');

        Route::post('/projects/{project}/report', [ProjectController::class, 'report'])
            ->name('projects.report');

        Route::post('/projects/{project}/endorse', [ProjectController::class, 'endorse'])
            ->name('projects.endorse');

        /*
        |--------------------------------------------------------------------------
        | Tecnologías (AJAX)
        |--------------------------------------------------------------------------
        */
        Route::get('/technologies', [TechnologyController::class, 'index'])
            ->name('technologies.index');
    });
});

require __DIR__ . '/auth.php';
