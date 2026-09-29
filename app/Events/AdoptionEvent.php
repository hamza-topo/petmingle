<?php

namespace App\Events;

use App\Enums\PusherEvent;
use App\Models\Adoption;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Events\ShouldDispatchAfterCommit;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AdoptionEvent implements ShouldBroadcastNow, ShouldDispatchAfterCommit
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
        return new Channel('new-adoption');
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
