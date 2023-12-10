<?php

namespace App\Observers;

use App\Models\Block;
use App\Repositories\LikeRepository;
use App\Repositories\MatchRepository;

class BlockObserver
{
    public function __construct(
        protected LikeRepository $likeRepository,
        protected MatchRepository $matchRepository
    ) {
    }
    /**
     * Handle the Block "created" event.
     *
     * @param  \App\Models\Block  $block
     * @return void
     */
    public function created(Block $block)
    {
        $this->likeRepository->dislike(['from' => $block->from ?? '', 'to' => $block->to ?? '']);
    }

    /**
     * Handle the Block "updated" event.
     *
     * @param  \App\Models\Block  $block
     * @return void
     */
    public function updated(Block $block)
    {
        //
    }

    /**
     * Handle the Block "deleted" event.
     *
     * @param  \App\Models\Block  $block
     * @return void
     */
    public function deleted(Block $block)
    {
        //
    }

    /**
     * Handle the Block "restored" event.
     *
     * @param  \App\Models\Block  $block
     * @return void
     */
    public function restored(Block $block)
    {
        //
    }

    /**
     * Handle the Block "force deleted" event.
     *
     * @param  \App\Models\Block  $block
     * @return void
     */
    public function forceDeleted(Block $block)
    {
        //
    }
}
