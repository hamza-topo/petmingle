<?php

namespace App\Policies;

use App\Models\Message;
use App\Models\User;

class MessagePolicy
{
    public function view(User $user, Message $message): bool
    {
        return $message->sender_id === $user->id
            || $message->receiver_id === $user->id;
    }

    public function update(User $user, Message $message): bool
    {
        return $message->sender_id === $user->id;
    }

    public function delete(User $user, Message $message): bool
    {
        return $message->sender_id === $user->id;
    }

    public function restore(User $user, Message $message): bool
    {
        return $message->sender_id === $user->id;
    }
}