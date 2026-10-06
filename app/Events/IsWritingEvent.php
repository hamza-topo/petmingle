<?php

namespace App\Events;

use App\Enums\PusherEvent;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Contracts\Broadcasting\ShouldRescue;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class IsWritingEvent implements ShouldBroadcastNow, ShouldRescue
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public int $senderUserId,
        private int $receiverUserId,
        public bool $isWriting = false
    ) {}

    public function broadcastOn(): PrivateChannel
    {
        return new PrivateChannel(
            'App.Models.User.' . $this->receiverUserId
        );
    }

    public function broadcastWith(): array
    {
        return [
            'sender_user_id' => $this->senderUserId,
            'receiver_user_id' => $this->receiverUserId,
            'is_writing' => $this->isWriting,
        ];
    }

    public function broadcastAs(): string
    {
        return PusherEvent::IS_WRITING_TO;
    }
}
