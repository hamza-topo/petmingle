<?php

namespace App\Providers;

use App\Models\Species;
use App\Observers\SpeciesObserver;
use Illuminate\Support\ServiceProvider;
use App\Services\CacheService;

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
            return new CacheService();
        });
    }

    public function boot()
    {
        Species::observe(SpeciesObserver::class);
    }
}
