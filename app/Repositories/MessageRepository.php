<?php

namespace App\Repositories;

use App\Models\Message;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class MessageRepository
{
    
    /**
     * create
     *
     * @param  mixed $chat
     * @return Message
     */
    public function create(array $chat): Message
    {
        return Message::create($chat);
    }
    
    /**
     * update
     *
     * @param  mixed $messageId
     * @param  mixed $newMessage
     * @return Message
     */
    public function update(int $messageId, array $newMessage): Message
    {
        $message = $this->getById($messageId);
        $message->update($newMessage);
        $message->refresh();

        return $message;
    }
    
    /**
     * getById
     *
     * @param  mixed $messageId
     * @return Message
     */
    public function getById(int $messageId): Message
    {
        return Message::find($messageId);
    }
    
    /**
     * delete
     *
     * @param  mixed $messageId
     * @return bool
     */
    public function delete(int $messageId): bool
    {
        return Message::destroy($messageId);
    }
    
    /**
     * restore
     *
     * @param  mixed $messageId
     * @return bool
     */
    public function restore(int $messageId): bool
    {
        return Message::withTrashed()->find($messageId)->restore();
    }
    
    /**
     * messages
     *
     * @param  mixed $senderId
     * @param  mixed $receiverId
     * @return LengthAwarePaginator
     */
    public function messages(int $senderId, int $receiverId): LengthAwarePaginator
    {
        return Message::where(['sender_id' => $senderId, 'receiver_id' => $receiverId])
        ->with(['receiver.pet', 'sender.pet'])
        ->paginate();
    }
}
