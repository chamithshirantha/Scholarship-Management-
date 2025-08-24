<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Bind repositories
        $this->app->bind(\App\Repositories\UserRepository::class, function ($app) {
            return new \App\Repositories\UserRepository();
        });
        $this->app->bind(\App\Repositories\ScholarshipRepository::class, function ($app) {
            return new \App\Repositories\ScholarshipRepository();
        });

        $this->app->bind(\App\Repositories\ApplicationRepository::class, function ($app) {
            return new \App\Repositories\ApplicationRepository();
        });

        $this->app->bind(\App\Repositories\AwardRepository::class, function ($app) {
            return new \App\Repositories\AwardRepository();
        });

        $this->app->bind(\App\Repositories\DisbursementRepository::class, function ($app) {
            return new \App\Repositories\DisbursementRepository();
        });

        // Bind services
        $this->app->bind(\App\Services\AuthService::class, function ($app) {
            return new \App\Services\AuthService(
                $app->make(\App\Repositories\UserRepository::class)
            );
        });
        $this->app->bind(\App\Services\ScholarshipService::class, function ($app) {
            return new \App\Services\ScholarshipService(
                $app->make(\App\Repositories\ScholarshipRepository::class)
            );
        });

        $this->app->bind(\App\Services\ApplicationService::class, function ($app) {
            return new \App\Services\ApplicationService(
                $app->make(\App\Repositories\ApplicationRepository::class)
            );
        });

        $this->app->bind(\App\Services\AwardService::class, function ($app) {
            return new \App\Services\AwardService(
                $app->make(\App\Repositories\AwardRepository::class)
            );
        });

        $this->app->bind(\App\Services\DisbursementService::class, function ($app) {
            return new \App\Services\DisbursementService(
                $app->make(\App\Repositories\DisbursementRepository::class)
            );
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
