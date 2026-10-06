<?php

namespace App\Providers;

use App\Services\CacheService;
use Illuminate\Support\ServiceProvider;

class CacheServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {

        $this->app->singleton(CacheService::class, function ($app) {
            return new CacheService;
        });
    }
}
