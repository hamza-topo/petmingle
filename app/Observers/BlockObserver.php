<?php

namespace App\Observers;

use App\Models\Block;
use App\Repositories\ConversationRepository;
use App\Repositories\LikeRepository;
use App\Repositories\MessageRepository;

class BlockObserver
{
    public function __construct(
        protected LikeRepository $likeRepository,
        protected ConversationRepository $conversationRepository,
        protected MessageRepository $messageRepository
    ) {}

    public function created(Block $block): void
    {
        $fromUserId = (int) $block->from;
        $toUserId = (int) $block->to;

        $this->conversationRepository->delete([
            'first_user_id' => $fromUserId,
            'seconde_user_id' => $toUserId,
        ]);

        $this->messageRepository->deleteBetween(
            $fromUserId,
            $toUserId
        );

        $fromPetId = $block->from()->first()?->pet?->id;
        $toPetId = $block->to()->first()?->pet?->id;

        if ($fromPetId === null || $toPetId === null) {
            return;
        }

        $this->likeRepository->dislike([
            'from' => $fromPetId,
            'to' => $toPetId,
        ]);

        $this->likeRepository->dislike([
            'from' => $toPetId,
            'to' => $fromPetId,
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
