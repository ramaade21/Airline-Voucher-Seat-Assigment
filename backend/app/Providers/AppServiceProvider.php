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
        // App\Services\SeatGeneratorService and App\Services\VoucherService
        // are resolved automatically by Laravel's container via
        // constructor-based dependency injection (auto-wiring), so no
        // explicit bindings are required here. They are listed for clarity:
        //
        // $this->app->singleton(\App\Services\SeatGeneratorService::class);
        // $this->app->singleton(\App\Services\VoucherService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
