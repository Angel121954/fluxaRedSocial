<?php

declare(strict_types=1);

use App\Http\Controllers\Admin\CompanyController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DiaryController as AdminDiaryController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\ContentController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Auth\GuestController;
use App\Http\Controllers\Auth\SocialAuthController;
use App\Http\Controllers\Diary\DiaryController;
use App\Http\Controllers\Explore\ExploreController;
use App\Http\Controllers\Feed\FeedController;
use App\Http\Controllers\Follows\FollowController;
use App\Http\Controllers\Jobs\JobController;
use App\Http\Controllers\Messages\MessageController;
use App\Http\Controllers\Notifications\NotificationController;
use App\Http\Controllers\Onboarding\OnboardingController;
use App\Http\Controllers\Pages\AboutFluxaController;
use App\Http\Controllers\Pages\ContactController;
use App\Http\Controllers\Pages\PrivacyPolicyController;
use App\Http\Controllers\Pages\ProblemReportController;
use App\Http\Controllers\Pages\TermsController;
use App\Http\Controllers\Profile\AccountController;
use App\Http\Controllers\Profile\ConfigurationController;
use App\Http\Controllers\Profile\CVSettingsController;
use App\Http\Controllers\Profile\EducationController;
use App\Http\Controllers\Profile\GitHubController;
use App\Http\Controllers\Profile\NotificationPreferenceController;
use App\Http\Controllers\Profile\PrivacyController;
use App\Http\Controllers\Profile\ProfileController;
use App\Http\Controllers\Profile\SecurityController;
use App\Http\Controllers\Profile\UserController;
use App\Http\Controllers\Profile\WorkExperienceController;
use App\Http\Controllers\Projects\CommentController;
use App\Http\Controllers\Projects\CommentLikeController;
use App\Http\Controllers\Projects\ProjectController;
use App\Http\Controllers\Salaries\SalaryController;
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
| Conexión GitHub para importar proyectos (antes que /auth/{provider})
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/auth/github/connect', [GitHubController::class, 'redirectToGitHub'])
        ->name('github.connect');
});

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

    Route::get('/onboarding/account-type', [OnboardingController::class, 'accountType'])
        ->name('onboarding.accountType');

    Route::post('/onboarding/account-type', [OnboardingController::class, 'saveAccountType'])
        ->name('onboarding.saveAccountType');

    Route::get('/onboarding/technologies', [OnboardingController::class, 'technologies'])
        ->name('onboarding.technologies');

    Route::post('/onboarding/technologies', [OnboardingController::class, 'saveTechnologies'])
        ->name('onboarding.saveTechnologies');

    Route::get('/onboarding/role', [OnboardingController::class, 'role'])
        ->name('onboarding.role');

    Route::post('/onboarding/role', [OnboardingController::class, 'saveRole'])
        ->name('onboarding.saveRole');

    Route::get('/onboarding/bio', [OnboardingController::class, 'bio'])
        ->name('onboarding.bio');

    Route::post('/onboarding/bio', [OnboardingController::class, 'saveBio'])
        ->name('onboarding.saveBio');

    Route::get('/onboarding/suggestions', [OnboardingController::class, 'suggestions'])
        ->name('onboarding.suggestions');

    Route::post('/onboarding/suggestions', [OnboardingController::class, 'saveSuggestions'])
        ->name('onboarding.saveSuggestions');

    Route::post('/users/{user}/follow', [FollowController::class, 'toggle'])
        ->name('users.follow')
        ->middleware('restrict.guest');
});

/*
|--------------------------------------------------------------------------
| CV
|--------------------------------------------------------------------------
*/
Route::get('/cv/preview-interno', [ProfileController::class, 'previewInterno'])
    ->name('cv.preview')
    ->middleware(['auth', 'verified', 'restrict.guest']);

Route::get('/cv/download/{username?}', [ProfileController::class, 'downloadCV'])
    ->name('cv.download.public')
    ->middleware(['auth', 'verified', 'restrict.guest']);

Route::get('/cv/download/{username}/{format}', [ProfileController::class, 'downloadCV'])
    ->name('cv.download.public.format')
    ->middleware(['auth', 'verified', 'restrict.guest'])
    ->where('format', 'pdf|ats|json');

/*
|--------------------------------------------------------------------------
| Rutas protegidas (auth + onboarding)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'prevent-back-history', 'onboarding'])->group(function () {

    Route::get('/admin/dashboard', DashboardController::class)
        ->name('admin.dashboard')
        ->middleware('admin');

    Route::get('/admin/users', [AdminUserController::class, 'index'])
        ->name('admin.users.index')
        ->middleware('admin');

    Route::patch('/admin/users/{user}/ban', [AdminUserController::class, 'ban'])
        ->name('admin.users.ban')
        ->middleware('admin');

    Route::patch('/admin/users/{user}/unban', [AdminUserController::class, 'unban'])
        ->name('admin.users.unban')
        ->middleware('admin');

    Route::post('/admin/users/grant-badge', [AdminUserController::class, 'grantBadge'])
        ->name('admin.users.grantBadge')
        ->middleware('admin');

    Route::get('/admin/companies', [CompanyController::class, 'index'])
        ->name('admin.companies.index')
        ->middleware('admin');

    Route::patch('/admin/companies/{user}/ban', [CompanyController::class, 'ban'])
        ->name('admin.companies.ban')
        ->middleware('admin');

    Route::patch('/admin/companies/{user}/unban', [CompanyController::class, 'unban'])
        ->name('admin.companies.unban')
        ->middleware('admin');

    /*
    |--------------------------------------------------------------------------
    | Reportes
    |--------------------------------------------------------------------------
    */
    Route::get('/admin/reports', [ReportController::class, 'index'])
        ->name('admin.reports.index')
        ->middleware('admin');

    Route::delete('/admin/reports/user/{userReport}', [ReportController::class, 'dismissUserReport'])
        ->name('admin.reports.user.dismiss')
        ->middleware('admin');

    Route::delete('/admin/reports/project/{projectReport}', [ReportController::class, 'dismissProjectReport'])
        ->name('admin.reports.project.dismiss')
        ->middleware('admin');

    Route::delete('/admin/reports/diary/{diaryReport}', [ReportController::class, 'dismissDiaryReport'])
        ->name('admin.reports.diary.dismiss')
        ->middleware('admin');

    Route::delete('/admin/reports/problem/{problemReport}', [ReportController::class, 'dismissProblemReport'])
        ->name('admin.reports.problem.dismiss')
        ->middleware('admin');

    Route::patch('/admin/reports/contact/{contact}/read', [ReportController::class, 'markContactRead'])
        ->name('admin.reports.contact.read')
        ->middleware('admin');

    Route::patch('/admin/reports/contact/{contact}/unread', [ReportController::class, 'markContactUnread'])
        ->name('admin.reports.contact.unread')
        ->middleware('admin');

    /*
    |--------------------------------------------------------------------------
    | Diario
    |--------------------------------------------------------------------------
    */
    Route::get('/admin/diary', [AdminDiaryController::class, 'adminIndex'])
        ->name('admin.diary.index')
        ->middleware('admin');

    Route::post('/admin/diary', [AdminDiaryController::class, 'adminStore'])
        ->name('admin.diary.store')
        ->middleware('admin');

    Route::patch('/admin/diary/{diary}/close', [AdminDiaryController::class, 'close'])
        ->name('admin.diary.close')
        ->middleware('admin');

    Route::patch('/admin/diary/{diary}', [AdminDiaryController::class, 'update'])
        ->name('admin.diary.update')
        ->middleware('admin');

    /*
    |--------------------------------------------------------------------------
    | Contenido
    |--------------------------------------------------------------------------
    */
    Route::get('/admin/content', [ContentController::class, 'index'])
        ->name('admin.content.index')
        ->middleware('admin');

    Route::delete('/admin/content/project/{project}', [ContentController::class, 'deleteProject'])
        ->name('admin.content.project.delete')
        ->middleware('admin');

    Route::patch('/admin/content/project/{project}/restore', [ContentController::class, 'restoreProject'])
        ->name('admin.content.project.restore')
        ->middleware('admin');

    Route::delete('/admin/content/comment/{comment}', [ContentController::class, 'deleteComment'])
        ->name('admin.content.comment.delete')
        ->middleware('admin');

    Route::get('/diary', [DiaryController::class, 'index'])
        ->name('diary.index');

    Route::post('/diary', [DiaryController::class, 'store'])
        ->name('diary.reply');

    Route::post('/diary/{response}/like', [DiaryController::class, 'like'])
        ->name('diary.response.like');

    Route::post('/diary/{response}/bookmark', [DiaryController::class, 'bookmark'])
        ->name('diary.response.bookmark');

    Route::get('/diary/{response}/comments', [DiaryController::class, 'comments'])
        ->name('diary.response.comments');

    Route::post('/diary/{response}/comments', [DiaryController::class, 'comment'])
        ->name('diary.response.comment');

    Route::post('/diary/comments/{comment}/like', [DiaryController::class, 'commentLike'])
        ->name('diary.response.comment.like');

    Route::delete('/diary/comments/{comment}', [DiaryController::class, 'commentDestroy'])
        ->name('diary.response.comment.destroy');

    Route::post('/diary/{response}/report', [DiaryController::class, 'report'])
        ->name('diary.response.report');

    Route::delete('/diary/{response}', [DiaryController::class, 'destroy'])
        ->name('diary.response.destroy');

    Route::get('/diary/load-more', [DiaryController::class, 'loadMore'])
        ->name('diary.loadMore');

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

    Route::get('/explore/map', [ExploreController::class, 'map'])
        ->name('explore.map');

    /* Route::get('/salaries', [SalaryController::class, 'index'])
        ->name('salaries.index');

    Route::get('/salaries/data', [SalaryController::class, 'data'])
        ->name('salaries.data');

    Route::post('/salaries', [SalaryController::class, 'store'])
        ->name('salaries.store'); */

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

    Route::post('/report-problem', [ProblemReportController::class, 'store'])
        ->name('report-problem.store');

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

        Route::post('/users/{user}/report', [UserController::class, 'report'])
            ->name('users.report');

        Route::get('/users/{user}/followers', [FollowController::class, 'followers'])
            ->name('users.followers');

        Route::get('/users/{user}/following', [FollowController::class, 'following'])
            ->name('users.following');

        Route::get('/users/{user}/projects', [ProfileController::class, 'projects'])
            ->name('users.projects');

        Route::delete('/profile/avatar', [ProfileController::class, 'destroyAvatar'])
            ->name('profile.avatar.destroy');

        Route::post('/profile/technologies', [ProfileController::class, 'updateTechnologies'])
            ->name('profile.technologies.update');

        Route::post('/profile/technologies/{technology}/favorite', [ProfileController::class, 'toggleFavoriteTechnology'])
            ->name('profile.technologies.favorite');

        /*
        |--------------------------------------------------------------------------
        | GitHub API (protected)
        |--------------------------------------------------------------------------
        */
        Route::get('/api/github/repos', [GitHubController::class, 'listRepos'])
            ->name('github.repos.list');
        Route::post('/api/github/repos/import', [GitHubController::class, 'importRepo'])
            ->name('github.repos.import');
        Route::post('/api/github/disconnect', [GitHubController::class, 'disconnect'])
            ->name('github.disconnect');

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

        Route::get('/notifications/list', [NotificationController::class, 'list'])
            ->name('notifications.list');

        Route::get('/notifications/unread', [NotificationController::class, 'unread'])
            ->name('notifications.unread');

        Route::patch('/notifications/{notification}/read', [NotificationController::class, 'markAsRead'])
            ->name('notifications.markAsRead');

        Route::patch('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])
            ->name('notifications.markAllAsRead');

        Route::delete('/notifications/{notification}', [NotificationController::class, 'destroy'])
            ->name('notifications.destroy');

        Route::get('/notification-preference', [NotificationPreferenceController::class, 'index'])
            ->name('notification-preference.index');

        Route::patch('/notification-preference', [NotificationPreferenceController::class, 'update'])
            ->name('notification-preference.update');

        /*
        |--------------------------------------------------------------------------
        | Bolsa de empleo
        |--------------------------------------------------------------------------
        */
        /* Route::get('/jobs', [JobController::class, 'index'])
            ->name('jobs.index');
        Route::get('/jobs/saved', [JobController::class, 'saved'])
            ->name('jobs.saved');
        Route::get('/jobs/create', [JobController::class, 'create'])
            ->name('jobs.create');
        Route::get('/jobs/{id}', [JobController::class, 'show'])
            ->name('jobs.show');
        Route::post('/jobs/bookmark', [JobController::class, 'bookmark'])
            ->name('jobs.bookmark');
        Route::post('/jobs', [JobController::class, 'store'])
            ->name('jobs.store'); */

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

        Route::delete('admin/suggestions/{suggestion}', [SuggestionController::class, 'destroy'])
            ->name('admin.suggestions.destroy')
            ->middleware('admin');

        /*
        |--------------------------------------------------------------------------
        | Buscador de usuarios para el sistema de mensajería
        |--------------------------------------------------------------------------
        */
        Route::get('/users/search', [UserController::class, 'search'])->name('users.search');

        /*
        |--------------------------------------------------------------------------
        | Sistema de mensajería
        |--------------------------------------------------------------------------
        */
        Route::get('/messages/projects', [MessageController::class, 'userProjects'])->name('messages.projects');
        Route::get('/messages', [MessageController::class, 'index'])->name('messages.index');
        Route::get('/messages/unread/count', [MessageController::class, 'unreadCount'])->name('messages.unreadCount');
        Route::post('/messages/viewing', [MessageController::class, 'setViewing'])->name('messages.setViewing');
        Route::post('/messages/viewing/clear', [MessageController::class, 'clearViewing'])->name('messages.clearViewing');
        Route::patch('/messages/{conversation}/read', [MessageController::class, 'markAsRead'])->name('messages.markAsRead');
        Route::patch('/messages/message/{message}/read', [MessageController::class, 'markMessageAsRead'])->name('messages.markMessageAsRead');
        Route::patch('/messages/message/{message}', [MessageController::class, 'update'])->name('messages.update');
        Route::post('/messages/{conversation}', [MessageController::class, 'store'])->name('messages.store')->middleware('idempotent');
        Route::post('/messages/{conversation}/media', [MessageController::class, 'storeMedia'])->name('messages.storeMedia')->middleware('idempotent');
        Route::post('/messages/{conversation}/gif', [MessageController::class, 'storeGif'])->name('messages.storeGif')->middleware('idempotent');
        Route::get('/messages/user/{username}', [MessageController::class, 'getOrCreateConversation'])->name('messages.user');
        Route::get('/messages/chat/{username}', [MessageController::class, 'redirectToConversation'])->name('messages.chat');
        Route::post('/messages/user/{user_id}', [MessageController::class, 'storeNewConversation'])->name('messages.storeNew')->middleware('idempotent');
        Route::post('/messages/{user}/block', [MessageController::class, 'blockUser'])->name('messages.block');

        /*
        |--------------------------------------------------------------------------
        | Recursos del perfil
        |--------------------------------------------------------------------------
        */
        Route::resource('work-experiences', WorkExperienceController::class);
        Route::get('/settings/cv', [CVSettingsController::class, 'edit'])->name('cv.edit');
        Route::put('/settings/cv', [CVSettingsController::class, 'update'])->name('cv.update');
        Route::get('/settings/cv/restore', [CVSettingsController::class, 'restore'])->name('cv.restore');
        Route::get('/settings/cv/download', [CVSettingsController::class, 'download'])->name('cv.download');
        Route::get('/settings/cv/download/{format}', [CVSettingsController::class, 'downloadFormat'])->name('cv.download.format')
            ->where('format', 'pdf|ats|json');
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

        Route::delete('/projects/{project}/media/{media}', [ProjectController::class, 'deleteMedia'])
            ->name('projects.media.destroy');

        /*
         |--------------------------------------------------------------------------
         | Comentarios
         |--------------------------------------------------------------------------
         */
        Route::get('/projects/{project}/comments', [CommentController::class, 'index'])
            ->name('comments.index');
        Route::post('/projects/{project}/comments', [CommentController::class, 'store'])
            ->name('comments.store');
        Route::post('/comments/{comment}/like', [CommentLikeController::class, 'toggle'])
            ->name('comments.like');
        Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])
            ->name('comments.destroy');

        /*
        |--------------------------------------------------------------------------
        | Tecnologías (AJAX)
        |--------------------------------------------------------------------------
        */
        Route::get('/technologies', [TechnologyController::class, 'index'])
            ->name('technologies.index');
    });
});

require __DIR__.'/auth.php';
