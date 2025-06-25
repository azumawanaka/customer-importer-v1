<?php

namespace App\Providers;

use App\Contracts\CustomerImporterInterface;
use App\Services\RandomUserImporter;
use Illuminate\Support\ServiceProvider;

class CustomerServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(CustomerImporterInterface::class, function ($app) {
            return new RandomUserImporter(
                config('services.randomuser.url'),
                config('services.randomuser.default_count')
            );
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
