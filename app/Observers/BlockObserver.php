<?php

namespace App\Observers;

use App\Models\Block;
use App\Repositories\ConversationRepository;
use App\Repositories\LikeRepository;

class BlockObserver
{
    public function __construct(
        protected LikeRepository $likeRepository,
        protected ConversationRepository $conversationRepository
    ) {
    }

    public function created(Block $block): void
    {
        $this->conversationRepository->delete([
            'first_user_id' => (int) $block->from,
            'seconde_user_id' => (int) $block->to,
        ]);

        $fromPetId = $block->from()->first()?->pet?->id;
        $toPetId = $block->to()->first()?->pet?->id;

        if ($fromPetId === null || $toPetId === null) {
            return;
        }

        $this->likeRepository->dislike([
            'from' => $fromPetId,
            'to' => $toPetId,
        ]);
    }

    public function updated(Block $block)
    {
        //
    }

    public function deleted(Block $block)
    {
        //
    }

    public function restored(Block $block)
    {
        //
    }

    public function forceDeleted(Block $block)
    {
        //
    }
}