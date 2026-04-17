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
            'profile.account',
            'profile.cv',
            'profile.configuration',
            'profile.educations',
            'profile.notification-preference',
            'profile.privacy',
            'profile.security',
            'profile.work-experiences',
        ], \App\View\Composers\ProfileComposer::class);
    }
}
