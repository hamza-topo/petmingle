<?php

namespace App\Http\Resources\Api\Message;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\ResourceCollection;

class Chat extends ResourceCollection
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return $this->collection->map(function ($message) {

            return [
                'conversation_id' => $message->conversation_id,
                'sender_id' => $message->sender_id,
                'receiver_id' => $message->receiver_id,
                'sender_name' => $message->sender->name,
                'receiver_name' => $message->receiver->name,
                'sender_profile_pic' => '',
                'receiver_profile_pic' => '',
                'pet_sender_id' => $message->sender->pet->id,
                'pet_receiver_id' => $message->receiver->pet->id,
                'pet_sender_name' => $message->sender->pet->name,
                'pet_receiver_name' => $message->receiver->pet->name,
                'pet_sender_profile_pic' => asset('storage/' .  $message->sender->pet->images[0]  ?? ''),
                'pet_receiver_profile_pic' => asset('storage/' .  $message->receiver->pet->images[0]  ?? '') ?? '',
                'message_created_at' => displayHumanDate($message->created_at),
            ];
        });
    }
}
