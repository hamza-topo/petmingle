<?php

namespace App\Providers;

use App\Models\Adoption;
use App\Models\Block;
use App\Models\Blog;
use App\Models\Like;
use App\Models\Message;
use App\Models\Species;
use App\Models\User;
use App\Observers\AdoptionObserver;
use App\Observers\BlockObserver;
use App\Observers\BlogObserver;
use App\Observers\LikeObserver;
use App\Observers\MessageObserver;
use App\Observers\SpeciesObserver;
use App\Observers\UserObserver;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [];

    public function boot(): void
    {
        Like::observe(LikeObserver::class);
        Block::observe(BlockObserver::class);
        Message::observe(MessageObserver::class);
        User::observe(UserObserver::class);
        Adoption::observe(AdoptionObserver::class);
        Blog::observe(BlogObserver::class);
        Species::observe(SpeciesObserver::class);
    }
}