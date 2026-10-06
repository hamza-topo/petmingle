<?php

namespace Tests\Feature\Api;

use App\Models\Block;
use App\Models\Conversation;
use App\Models\Like;
use App\Models\MatchTable;
use App\Models\Message;
use App\Models\Pet;
use App\Models\Race;
use App\Models\Species;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class BlockedInteractionTest extends TestCase
{
    use RefreshDatabase;

    public function test_blocker_and_blocked_account_cannot_like_each_other(): void
    {
        [$firstUser, $firstPet] = $this->userWithPet('First');
        [$secondUser, $secondPet] = $this->userWithPet('Second');

        Block::withoutEvents(fn () => Block::create([
            'from' => $firstUser->id,
            'to' => $secondUser->id,
        ]));

        Sanctum::actingAs($firstUser);

        $this->postJson('/api/v.0/likes', [
            'to_pet_id' => $secondPet->id,
        ])
            ->assertUnprocessable()
            ->assertJsonStructure([
                'errors' => ['to_pet_id'],
            ]);

        Sanctum::actingAs($secondUser);

        $this->postJson('/api/v.0/likes', [
            'to_pet_id' => $firstPet->id,
        ])
            ->assertUnprocessable()
            ->assertJsonStructure([
                'errors' => ['to_pet_id'],
            ]);

        $this->assertDatabaseCount('likes', 0);
    }

    public function test_reverse_like_alone_does_not_allow_messaging(): void
    {
        [$sender, $senderPet] = $this->userWithPet('Sender');
        [$receiver, $receiverPet] = $this->userWithPet('Receiver');

        Like::withoutEvents(fn () => Like::create([
            'from' => $receiverPet->id,
            'to' => $senderPet->id,
        ]));

        Sanctum::actingAs($sender);

        $this->postJson('/api/v.0/messages', [
            'receiver_id' => $receiver->id,
            'content' => 'Should not be sent',
        ])
            ->assertUnprocessable()
            ->assertJsonStructure([
                'errors' => ['receiver_id'],
            ]);

        $this->assertDatabaseCount('messages', 0);
    }

    public function test_blocked_match_cannot_send_or_read_message_thread(): void
    {
        [$sender, $senderPet] = $this->userWithPet('Sender');
        [$receiver, $receiverPet] = $this->userWithPet('Receiver');

        $this->establishMatch($senderPet, $receiverPet);

        $conversation = Conversation::create([
            'first_user_id' => $sender->id,
            'seconde_user_id' => $receiver->id,
        ]);

        Message::withoutEvents(fn () => Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $sender->id,
            'receiver_id' => $receiver->id,
            'content' => 'Existing message',
            'is_seen' => false,
        ]));

        Block::withoutEvents(fn () => Block::create([
            'from' => $receiver->id,
            'to' => $sender->id,
        ]));

        Sanctum::actingAs($sender);

        $this->postJson('/api/v.0/messages', [
            'receiver_id' => $receiver->id,
            'content' => 'Blocked message',
        ])
            ->assertUnprocessable()
            ->assertJsonStructure([
                'errors' => ['receiver_id'],
            ]);

        $this->getJson(
            '/api/v.0/messages?receiver_id=' . $receiver->id
        )
            ->assertUnprocessable()
            ->assertJsonStructure([
                'errors' => ['receiver_id'],
            ]);
    }

    public function test_block_severs_relationship_and_archives_contact_history(): void
    {
        [$firstUser, $firstPet] = $this->userWithPet('First');
        [$secondUser, $secondPet] = $this->userWithPet('Second');

        Like::withoutEvents(function () use (
            $firstPet,
            $secondPet
        ) {
            Like::create([
                'from' => $firstPet->id,
                'to' => $secondPet->id,
            ]);

            Like::create([
                'from' => $secondPet->id,
                'to' => $firstPet->id,
            ]);
        });

        $this->establishMatch($firstPet, $secondPet);

        $conversation = Conversation::create([
            'first_user_id' => $firstUser->id,
            'seconde_user_id' => $secondUser->id,
        ]);

        $firstMessage = Message::withoutEvents(
            fn () => Message::create([
                'conversation_id' => $conversation->id,
                'sender_id' => $firstUser->id,
                'receiver_id' => $secondUser->id,
                'content' => 'First direction',
                'is_seen' => false,
            ])
        );

        $secondMessage = Message::withoutEvents(
            fn () => Message::create([
                'conversation_id' => $conversation->id,
                'sender_id' => $secondUser->id,
                'receiver_id' => $firstUser->id,
                'content' => 'Second direction',
                'is_seen' => false,
            ])
        );

        Block::create([
            'from' => $firstUser->id,
            'to' => $secondUser->id,
        ]);

        foreach ([
            [$firstPet->id, $secondPet->id],
            [$secondPet->id, $firstPet->id],
        ] as [$fromPetId, $toPetId]) {
            $this->assertSoftDeleted('likes', [
                'from' => $fromPetId,
                'to' => $toPetId,
            ]);

            $this->assertSoftDeleted('matches', [
                'from' => $fromPetId,
                'to' => $toPetId,
            ]);
        }

        $this->assertSoftDeleted('conversations', [
            'id' => $conversation->id,
        ]);

        $this->assertSoftDeleted('messages', [
            'id' => $firstMessage->id,
        ]);

        $this->assertSoftDeleted('messages', [
            'id' => $secondMessage->id,
        ]);
    }

    public function test_blocked_sender_cannot_restore_or_edit_old_message(): void
    {
        [$sender] = $this->userWithPet('Sender');
        [$receiver] = $this->userWithPet('Receiver');

        $conversation = Conversation::create([
            'first_user_id' => $sender->id,
            'seconde_user_id' => $receiver->id,
        ]);

        $message = Message::withoutEvents(
            fn () => Message::create([
                'conversation_id' => $conversation->id,
                'sender_id' => $sender->id,
                'receiver_id' => $receiver->id,
                'content' => 'Historical message',
                'is_seen' => false,
            ])
        );

        Block::withoutEvents(fn () => Block::create([
            'from' => $receiver->id,
            'to' => $sender->id,
        ]));

        Sanctum::actingAs($sender);

        $this->putJson('/api/v.0/messages/' . $message->id, [
            'content' => 'Edited after block',
        ])->assertForbidden();

        $message->delete();

        $this->putJson(
            '/api/v.0/messages/restore/' . $message->id
        )->assertForbidden();

        $this->assertSoftDeleted('messages', [
            'id' => $message->id,
        ]);
    }

    private function establishMatch(
        Pet $firstPet,
        Pet $secondPet
    ): void {
        MatchTable::withoutEvents(function () use (
            $firstPet,
            $secondPet
        ) {
            MatchTable::create([
                'from' => $firstPet->id,
                'to' => $secondPet->id,
            ]);

            MatchTable::create([
                'from' => $secondPet->id,
                'to' => $firstPet->id,
            ]);
        });
    }

    private function userWithPet(string $name): array
    {
        $user = User::withoutEvents(
            fn () => User::factory()->create([
                'name' => $name,
            ])
        );

        $species = Species::create([
            'name' => 'Species-' . $user->id,
        ]);

        $race = Race::create([
            'species_id' => $species->id,
            'name' => 'Race-' . $user->id,
        ]);

        $pet = Pet::withoutEvents(
            fn () => Pet::create([
                'user_id' => $user->id,
                'species_id' => $species->id,
                'race_id' => $race->id,
                'name' => $name . ' pet',
                'age' => 3,
                'sexe' => 1,
                'color' => 'brown',
                'images' => [],
                'about' => 'Test pet',
            ])
        );

        return [$user, $pet];
    }
}
