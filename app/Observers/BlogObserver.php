<?php

namespace App\Observers;

use App\Events\AutoSiteMapEvent;
use App\Models\Blog;

class BlogObserver
{
    public function __construct()
    {
        // dispatch(new AutoSiteMapEvent());
    }

    /**
     * Handle the Blog "created" event.
     *
     * @return void
     */
    public function created(Blog $blog)
    {
        //
    }

    /**
     * Handle the Blog "updated" event.
     *
     * @return void
     */
    public function updated(Blog $blog)
    {
        //
    }

    /**
     * Handle the Blog "deleted" event.
     *
     * @return void
     */
    public function deleted(Blog $blog)
    {
        //
    }

    /**
     * Handle the Blog "restored" event.
     *
     * @return void
     */
    public function restored(Blog $blog)
    {
        //
    }

    /**
     * Handle the Blog "force deleted" event.
     *
     * @return void
     */
    public function forceDeleted(Blog $blog)
    {
        //
    }
}
