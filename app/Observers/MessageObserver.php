<?php

namespace App\Observers;

use App\Models\Message;
use App\Services\MessageService;
use Illuminate\Contracts\Events\ShouldHandleEventsAfterCommit;

class MessageObserver implements ShouldHandleEventsAfterCommit
{
    public function __construct(protected MessageService $messageService) {}

    /**
     * Handle the Message "created" event.
     */
    public function created(Message $message): void
    {
        $this->messageService->notify($message);
    }

    /**
     * Handle the Message "updated" event.
     *
     * @return void
     */
    public function updated(Message $message)
    {
        //
    }

    /**
     * Handle the Message "deleted" event.
     *
     * @return void
     */
    public function deleted(Message $message)
    {
        //
    }

    /**
     * Handle the Message "restored" event.
     *
     * @return void
     */
    public function restored(Message $message)
    {
        //
    }

    /**
     * Handle the Message "force deleted" event.
     *
     * @return void
     */
    public function forceDeleted(Message $message)
    {
        //
    }
}
