<?php

namespace App\Providers;

use App\Services\Account\AccountService;
use App\Services\Account\AccountServiceInterface;
use App\Services\EveOnline\AuthHelpers\JwksMetadataService;
use Illuminate\Http\Resources\Json\JsonResource;
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
        $this->app->singleton(AccountServiceInterface::class, AccountService::class);
        $this->app->singleton(JwksMetadataService::class, function () {
            $cfg = config('services.eveonline');

            return new JwksMetadataService(
                metadataUrl: $cfg['metadata_url'],
                cacheTime: (int) $cfg['metadata_cache_time']
            );
        });
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

        JsonResource::withoutWrapping();
    }
}
