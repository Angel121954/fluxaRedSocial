<?php

namespace App\Providers;

use App\View\Composers\CvTemplateComposer;
use App\View\Composers\ProfileComposer;
use App\View\Composers\TopbarComposer;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (app()->environment('production')) {
            URL::forceScheme('https');
        }

        View::composer('components.cv-template', CvTemplateComposer::class);
        View::composer('components.topbar', TopbarComposer::class);

        View::composer([
            'profile.index',
            'profile.edit',
            'profile.educations',
            'profile.work-experiences',
            'settings.account',
            'settings.configuration',
            'settings.notification-preference',
            'settings.privacy',
            'settings.security',
            'cv.cv',
            'admin.suggestions.index',
            'admin.suggestions.create',
            'suggestions.create',
            'notifications.index',
            'public.about-fluxa',
            'public.contact',
            'public.privacy-policy',
            'public.terms',
            'messages.index',
        ], ProfileComposer::class);
    }
}
