<?php

namespace App\Events;

use App\Models\MatchTable;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Contracts\Broadcasting\ShouldRescue;
use Illuminate\Contracts\Events\ShouldDispatchAfterCommit;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MatchEvent implements ShouldBroadcastNow, ShouldDispatchAfterCommit, ShouldRescue
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(public MatchTable $fromMatch, public MatchTable $toMatch) {}

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn()
    {
        // Match IDs reference pets; subscriptions belong to their human owners.
        $owners = array_filter([
            $this->fromMatch->fromPet?->user_id,
            $this->fromMatch->toPet?->user_id,
        ]);

        return array_map(
            fn ($id) => new PrivateChannel('App.Models.User.'.$id),
            array_values(array_unique($owners))
        );
    }

    public function broadcastWith(): array
    {
        $fields = ['id', 'from', 'to', 'created_at', 'updated_at', 'deleted_at'];

        return [
            'fromMatch' => $this->fromMatch->only($fields),
            'toMatch' => $this->toMatch->only($fields),
        ];
    }

    public function broadcastAs()
    {
        return 'new.match';
    }
}
