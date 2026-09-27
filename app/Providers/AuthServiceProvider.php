<?php

namespace App\Providers;

use App\Models\Pet;
use App\Policies\PetPolicy;
use App\Models\Message;
use App\Policies\MessagePolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use App\Models\Location;
use App\Policies\LocationPolicy;
use App\Models\User;
use App\Policies\UserPolicy;
use App\Models\Adoption;
use App\Policies\AdoptionPolicy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Pet::class => PetPolicy::class,
        Message::class => MessagePolicy::class,
        Location::class => LocationPolicy::class,
        User::class => UserPolicy::class,
        Adoption::class => AdoptionPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        //
    }
}
