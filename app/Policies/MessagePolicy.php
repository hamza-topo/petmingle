<?php

namespace App\Policies;

use App\Models\Message;
use App\Models\User;
use App\Services\InteractionPolicy;

class MessagePolicy
{
    public function __construct(
        protected InteractionPolicy $interactionPolicy
    ) {}

    public function view(User $user, Message $message): bool
    {
        return (
            $message->sender_id === $user->id
            || $message->receiver_id === $user->id
        ) && !$this->interactionPolicy->isBlockedBetween(
            $user->id,
            $this->otherParticipantId($user, $message)
        );
    }

    public function update(User $user, Message $message): bool
    {
        return $message->sender_id === $user->id
            && !$this->interactionPolicy->isBlockedBetween(
                $user->id,
                (int) $message->receiver_id
            );
    }

    public function delete(User $user, Message $message): bool
    {
        return $message->sender_id === $user->id;
    }

    public function restore(User $user, Message $message): bool
    {
        return $message->sender_id === $user->id
            && !$this->interactionPolicy->isBlockedBetween(
                $user->id,
                (int) $message->receiver_id
            );
    }

    private function otherParticipantId(
        User $user,
        Message $message
    ): int {
        return $message->sender_id === $user->id
            ? (int) $message->receiver_id
            : (int) $message->sender_id;
    }
}
