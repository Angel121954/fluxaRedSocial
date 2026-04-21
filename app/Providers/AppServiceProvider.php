<?php

namespace App\Providers;

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
     *
     * @return void
     */
    public function boot()
    {
        View::composer('components.cv-template', \App\View\Composers\CvTemplateComposer::class);

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
        ], \App\View\Composers\ProfileComposer::class);
    }
}
