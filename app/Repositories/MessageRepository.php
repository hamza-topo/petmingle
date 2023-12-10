<?php

namespace App\Repositories;

use App\Models\Message;
use Illuminate\Support\Collection;

class MessageRepository
{

    public function create(array $chat): Message
    {
        return Message::create($chat);
    }

    public function update(int $messageId, array $newMessage): Message
    {
        $message = $this->getById($messageId);
        $message->update($newMessage);
        $message->refresh();

        return $message;
    }

    public function getById(int $messageId): Message
    {
        return Message::find($messageId);
    }

    public function delete(int $messageId): bool
    {
        return Message::destroy($messageId);
    }

    public function restore(int $messageId): bool
    {
        return Message::withTrashed()->find($messageId)->restore();
    }

    public function messages(int $senderId, int $receiverId): Collection
    {
        return Message::where(['sender_id' => $senderId, 'receiver_id' => $receiverId])->paginate();
    }
}
