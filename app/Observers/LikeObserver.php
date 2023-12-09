<?php

namespace App\Observers;

use App\Events\MatchEvent;
use App\Models\Like;
use App\Repositories\LikeRepository;
use App\Repositories\MatchRepository;

class LikeObserver
{
    public function __construct(
        protected LikeRepository $likeRepository,
        protected MatchRepository $matchRepository,

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
            $likeFirst = $like->toArray();
            $likeSeconde = ['from' => $likeFirst['to'], 'to' => $likeFirst['to']];
            $fromMatch = $this->matchRepository->create($likeFirst);
            $toMatch = $this->matchRepository->create($likeSeconde);
            MatchEvent::dispatch($fromMatch, $toMatch);
        }
        //add try catch 
        //TODO::dispatch event normally 
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
