<?php

namespace App\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use SocialiteProviders\Eveonline\Provider;
use SocialiteProviders\Manager\SocialiteWasCalled;
use Firebase\JWT\JWT;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (class_exists(JWT::class)) {
            JWT::$leeway = 60; // seconds; keep this conservative
        }

        Event::listen(static function (SocialiteWasCalled $socialite) {
           $socialite->extendSocialite('eveonline', Provider::class);
        });
    }
}
