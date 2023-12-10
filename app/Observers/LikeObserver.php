<?php

namespace App\Observers;

use App\Models\Like;
use App\Repositories\LikeRepository;
use App\Services\MatchService;
use Illuminate\Support\Facades\Log;

class LikeObserver
{
    public function __construct(
        protected LikeRepository $likeRepository,
        protected MatchService $matchService,

    ) {
    }
    /**
     * Handle the Like "created" event.
     *
     * @param  \App\Models\Like  $like
     * @return void
     */
    public function created(Like $like)
    {
        if ($this->likeRepository->isMatch($like->toArray())) {
            try {
                $this->matchService
                    ->create($like->toArray())
                    // ->notify()
                    ->mail();
            } catch (\Exception $e) {
                Log::error('sending mail is matching failed: '.$e->getMessage());
                return $e->getMessage();
            }
        }
    }

    /**
     * Handle the Like "updated" event.
     *
     * @param  \App\Models\Like  $like
     * @return void
     */
    public function updated(Like $like)
    {
        //
    }

    /**
     * Handle the Like "deleted" event.
     *
     * @param  \App\Models\Like  $like
     * @return void
     */
    public function deleted(Like $like)
    {
        //
    }

    /**
     * Handle the Like "restored" event.
     *
     * @param  \App\Models\Like  $like
     * @return void
     */
    public function restored(Like $like)
    {
        //
    }

    /**
     * Handle the Like "force deleted" event.
     *
     * @param  \App\Models\Like  $like
     * @return void
     */
    public function forceDeleted(Like $like)
    {
        //
    }
}
