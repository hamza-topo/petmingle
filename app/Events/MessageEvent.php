<?php

namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Contracts\Broadcasting\ShouldRescue;
use Illuminate\Contracts\Events\ShouldDispatchAfterCommit;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageEvent implements ShouldBroadcastNow, ShouldDispatchAfterCommit, ShouldRescue
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(public Message $message) {}

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn()
    {
        return array_map(
            fn ($id) => new PrivateChannel('App.Models.User.'.$id),
            array_unique([$this->message->sender_id, $this->message->receiver_id])
        );
    }

    public function broadcastWith(): array
    {
        return ['message' => $this->message->only([
            'id', 'conversation_id', 'sender_id', 'receiver_id', 'content',
            'is_seen', 'created_at', 'updated_at', 'deleted_at',
        ])];
    }

    public function broadcastAs()
    {
        return 'new.message';
    }
}
