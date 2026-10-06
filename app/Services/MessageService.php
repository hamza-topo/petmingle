<?php

namespace App\Services;

use App\Events\MessageEvent;
use App\Models\Message;

class MessageService
{
    /**
     * notify
     *
     * @param  mixed  $message
     */
    public function notify(Message $message): void
    {
        MessageEvent::dispatch($message);
    }
}
