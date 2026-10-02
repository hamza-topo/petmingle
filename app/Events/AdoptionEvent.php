<?php

namespace App\Events;

use App\Enums\PusherEvent;
use App\Models\Adoption;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Events\ShouldDispatchAfterCommit;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AdoptionEvent implements ShouldBroadcast, ShouldDispatchAfterCommit
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(
        public Adoption $adoption
    ) {
        //
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return array_map(
            fn ($id) => new PrivateChannel('App.Models.User.' . $id),
            array_unique([$this->adoption->from, $this->adoption->to])
        );
    }

    public function broadcastWith(): array
    {
        return ['adoption' => $this->adoption->only([
            'id', 'from', 'pet_id', 'to', 'created_at', 'updated_at', 'deleted_at',
        ])];
    }

    /**
     * Broadcast As channel
     * @author hamza <hamzaaitsidisaid.11@gmail.com>
     * @return string
     */
    public function broadcastAs(): string
    {
        return PusherEvent::ITS_NEW_ADOPTION;
    }
}
