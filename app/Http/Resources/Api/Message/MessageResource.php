<?php

namespace App\Http\Resources\Api\Message;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MessageResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => (int) $this->id,
            'conversation_id' => (int) $this->conversation_id,
            'sender_user_id' => (int) $this->sender_id,
            'receiver_user_id' => (int) $this->receiver_id,
            'content' => $this->content,
            'is_seen' => (bool) $this->is_seen,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
