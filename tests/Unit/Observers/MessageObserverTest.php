<?php

namespace Tests\Unit\Observers;

use App\Models\Message;
use App\Observers\MessageObserver;
use App\Services\MessageService;
use Tests\TestCase;

class MessageObserverTest extends TestCase
{
    public function test_created_message_triggers_notification(): void
    {
        $message = new Message([
            'sender_id' => 1,
            'receiver_id' => 2,
            'content' => 'Hello',
        ]);

        $messageService = $this->mock(MessageService::class);

        $messageService
            ->shouldReceive('notify')
            ->once()
            ->with($message);

        $observer = new MessageObserver($messageService);

        $observer->created($message);
    }
}
