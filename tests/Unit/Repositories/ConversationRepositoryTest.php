<?php

namespace Tests\Unit\Repositories;

use App\Models\Conversation;
use App\Models\User;
use App\Repositories\ConversationRepository;
use Illuminate\Foundation\Testing\DatabaseTruncation;
use Tests\TestCase;

class ConversationRepositoryTest extends TestCase
{
    use DatabaseTruncation;

    public function test_delete_archives_conversation_in_direct_order(): void
    {
        $firstUser = User::withoutEvents(
            fn () => User::factory()->create()
        );

        $secondUser = User::withoutEvents(
            fn () => User::factory()->create()
        );

        $conversation = Conversation::create([
            'first_user_id' => $firstUser->id,
            'seconde_user_id' => $secondUser->id,
        ]);

        $repository = app(ConversationRepository::class);

        $deleted = $repository->delete([
            'first_user_id' => $firstUser->id,
            'seconde_user_id' => $secondUser->id,
        ]);

        $this->assertSame(1, $deleted);

        $this->assertSoftDeleted('conversations', [
            'id' => $conversation->id,
        ]);
    }

    public function test_delete_archives_conversation_in_reverse_order(): void
    {
        $firstUser = User::withoutEvents(
            fn () => User::factory()->create()
        );

        $secondUser = User::withoutEvents(
            fn () => User::factory()->create()
        );

        $conversation = Conversation::create([
            'first_user_id' => $firstUser->id,
            'seconde_user_id' => $secondUser->id,
        ]);

        $repository = app(ConversationRepository::class);

        $deleted = $repository->delete([
            'first_user_id' => $secondUser->id,
            'seconde_user_id' => $firstUser->id,
        ]);

        $this->assertSame(1, $deleted);

        $this->assertSoftDeleted('conversations', [
            'id' => $conversation->id,
        ]);
    }
}