<?php

namespace App\Repositories;

use App\Models\Message;
use Illuminate\Pagination\LengthAwarePaginator;

class MessageRepository
{
    public function create(array $chat): Message
    {
        return Message::create($chat);
    }

    public function update(
        int $messageId,
        array $newMessage
    ): Message {
        $message = $this->getById($messageId);
        $message->update($newMessage);
        $message->refresh();

        return $message;
    }

    public function getById(int $messageId): Message
    {
        return Message::findOrFail($messageId);
    }

    public function delete(int $messageId): bool
    {
        return Message::destroy($messageId);
    }

    public function deleteBetween(
        int $firstUserId,
        int $secondUserId
    ): int {
        return Message::query()
            ->where(function ($query) use (
                $firstUserId,
                $secondUserId
            ) {
                $query
                    ->where([
                        'sender_id' => $firstUserId,
                        'receiver_id' => $secondUserId,
                    ])
                    ->orWhere([
                        'sender_id' => $secondUserId,
                        'receiver_id' => $firstUserId,
                    ]);
            })
            ->delete();
    }

    public function restore(int $messageId): bool
    {
        return $this->getByIdWithTrashed($messageId)->restore();
    }

    public function getByIdWithTrashed(int $messageId): Message
    {
        return Message::withTrashed()->findOrFail($messageId);
    }

    public function messages(
        int $senderId,
        int $receiverId
    ): LengthAwarePaginator {
        return Message::where([
            'sender_id' => $senderId,
            'receiver_id' => $receiverId,
        ])
            ->with(['receiver.pet', 'sender.pet'])
            ->paginate();
    }
}
