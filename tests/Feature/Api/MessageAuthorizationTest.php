<?php

namespace Tests\Feature\Api;

use App\Events\MessageEvent;
use App\Models\Conversation;
use App\Models\MatchTable;
use App\Models\Message;
use App\Models\Pet;
use App\Models\Race;
use App\Models\Species;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class MessageAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Event::fake([
            MessageEvent::class,
        ]);
    }

    public function test_sender_can_update_own_message(): void
    {
        $sender = User::factory()->create();
        $receiver = User::factory()->create();

        $conversation = Conversation::create([
            'first_user_id' => $sender->id,
            'seconde_user_id' => $receiver->id,
        ]);

        $message = Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $sender->id,
            'receiver_id' => $receiver->id,
            'content' => 'Original message',
            'is_seen' => false,
        ]);

        Sanctum::actingAs($sender);

        $this->putJson("/api/v.0/messages/{$message->id}", [
            'content' => 'Updated message',
        ])->assertOk();

        $this->assertDatabaseHas('messages', [
            'id' => $message->id,
            'content' => 'Updated message',
        ]);
    }

    public function test_receiver_cannot_update_message(): void
    {
        $sender = User::factory()->create();
        $receiver = User::factory()->create();

        $conversation = Conversation::create([
            'first_user_id' => $sender->id,
            'seconde_user_id' => $receiver->id,
        ]);

        $message = Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $sender->id,
            'receiver_id' => $receiver->id,
            'content' => 'Original message',
            'is_seen' => false,
        ]);

        Sanctum::actingAs($receiver);

        $this->putJson("/api/v.0/messages/{$message->id}", [
            'content' => 'Unauthorized edit',
        ])->assertForbidden();

        $this->assertDatabaseHas('messages', [
            'id' => $message->id,
            'content' => 'Original message',
        ]);
    }

    public function test_unrelated_user_cannot_update_message(): void
    {
        $sender = User::factory()->create();
        $receiver = User::factory()->create();
        $otherUser = User::factory()->create();

        $conversation = Conversation::create([
            'first_user_id' => $sender->id,
            'seconde_user_id' => $receiver->id,
        ]);

        $message = Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $sender->id,
            'receiver_id' => $receiver->id,
            'content' => 'Original message',
            'is_seen' => false,
        ]);

        Sanctum::actingAs($otherUser);

        $this->putJson("/api/v.0/messages/{$message->id}", [
            'content' => 'Unauthorized edit',
        ])->assertForbidden();
    }

    public function test_sender_can_delete_own_message(): void
    {
        $sender = User::factory()->create();
        $receiver = User::factory()->create();

        $conversation = Conversation::create([
            'first_user_id' => $sender->id,
            'seconde_user_id' => $receiver->id,
        ]);

        $message = Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $sender->id,
            'receiver_id' => $receiver->id,
            'content' => 'Message to delete',
            'is_seen' => false,
        ]);

        Sanctum::actingAs($sender);

        $this->deleteJson("/api/v.0/messages/{$message->id}")
            ->assertOk();

        $this->assertSoftDeleted('messages', [
            'id' => $message->id,
        ]);
    }

    public function test_receiver_cannot_delete_message(): void
    {
        $sender = User::factory()->create();
        $receiver = User::factory()->create();

        $conversation = Conversation::create([
            'first_user_id' => $sender->id,
            'seconde_user_id' => $receiver->id,
        ]);

        $message = Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $sender->id,
            'receiver_id' => $receiver->id,
            'content' => 'Protected message',
            'is_seen' => false,
        ]);

        Sanctum::actingAs($receiver);

        $this->deleteJson("/api/v.0/messages/{$message->id}")
            ->assertForbidden();

        $this->assertDatabaseHas('messages', [
            'id' => $message->id,
            'deleted_at' => null,
        ]);
    }

    public function test_sender_can_restore_own_message(): void
    {
        $sender = User::factory()->create();
        $receiver = User::factory()->create();

        $conversation = Conversation::create([
            'first_user_id' => $sender->id,
            'seconde_user_id' => $receiver->id,
        ]);

        $message = Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $sender->id,
            'receiver_id' => $receiver->id,
            'content' => 'Deleted message',
            'is_seen' => false,
        ]);

        $message->delete();

        Sanctum::actingAs($sender);

        $this->putJson("/api/v.0/messages/restore/{$message->id}")
            ->assertOk();

        $this->assertDatabaseHas('messages', [
            'id' => $message->id,
            'deleted_at' => null,
        ]);
    }

    public function test_receiver_cannot_restore_message(): void
    {
        $sender = User::factory()->create();
        $receiver = User::factory()->create();

        $conversation = Conversation::create([
            'first_user_id' => $sender->id,
            'seconde_user_id' => $receiver->id,
        ]);

        $message = Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $sender->id,
            'receiver_id' => $receiver->id,
            'content' => 'Deleted protected message',
            'is_seen' => false,
        ]);

        $message->delete();

        Sanctum::actingAs($receiver);

        $this->putJson("/api/v.0/messages/restore/{$message->id}")
            ->assertForbidden();

        $this->assertSoftDeleted('messages', [
            'id' => $message->id,
        ]);
    }

    public function test_authenticated_user_can_create_message_without_sender_id(): void
    {
        $sender = User::factory()->create();
        $receiver = User::factory()->create();
        $senderPet = $this->createPet($sender);
        $receiverPet = $this->createPet($receiver);

        $this->establishMatch($senderPet, $receiverPet);

        Sanctum::actingAs($sender);

        $this->postJson('/api/v.0/messages', [
            'receiver_id' => $receiver->id,
            'content' => 'Hello',
        ])->assertOk();

        $this->assertDatabaseHas('messages', [
            'sender_id' => $sender->id,
            'receiver_id' => $receiver->id,
            'content' => 'Hello',
        ]);
    }

    public function test_client_cannot_spoof_message_sender_id(): void
    {
        $sender = User::factory()->create();
        $receiver = User::factory()->create();
        $spoofedUser = User::factory()->create();

        $senderPet = $this->createPet($sender);
        $receiverPet = $this->createPet($receiver);

        $this->establishMatch($senderPet, $receiverPet);

        Sanctum::actingAs($sender);

        $this->postJson('/api/v.0/messages', [
            'sender_id' => $spoofedUser->id,
            'receiver_id' => $receiver->id,
            'content' => 'Hello',
        ])->assertOk();

        $this->assertDatabaseHas('messages', [
            'sender_id' => $sender->id,
            'receiver_id' => $receiver->id,
            'content' => 'Hello',
        ]);

        $this->assertDatabaseMissing('messages', [
            'sender_id' => $spoofedUser->id,
            'content' => 'Hello',
        ]);
    }

    public function test_sender_can_edit_content_but_cannot_reassign_message_or_mark_it_read(): void
    {
        $sender = User::factory()->create();
        $receiver = User::factory()->create();
        $outsider = User::factory()->create();
        $conversation = Conversation::create(['first_user_id' => $sender->id, 'seconde_user_id' => $receiver->id]);
        $otherConversation = Conversation::create(['first_user_id' => $outsider->id, 'seconde_user_id' => $receiver->id]);
        $message = Message::create([
            'conversation_id' => $conversation->id, 'sender_id' => $sender->id,
            'receiver_id' => $receiver->id, 'content' => 'Original', 'is_seen' => false,
        ]);
        Sanctum::actingAs($sender);

        foreach (['PUT', 'PATCH'] as $method) {
            $this->json($method, '/api/v.0/messages/'.$message->id, [
                'content' => 'Edited with '.$method, 'sender_id' => $outsider->id,
                'receiver_id' => $outsider->id, 'conversation_id' => $otherConversation->id,
                'is_seen' => true, 'first_user_id' => $outsider->id, 'seconde_user_id' => $outsider->id,
            ])->assertOk();

            $message->refresh();
            $this->assertSame('Edited with '.$method, $message->content);
            $this->assertSame($sender->id, $message->sender_id);
            $this->assertSame($receiver->id, $message->receiver_id);
            $this->assertSame($conversation->id, $message->conversation_id);
            $this->assertFalse((bool) $message->is_seen);
            $this->assertSame($sender->id, $conversation->fresh()->first_user_id);
            $this->assertSame($receiver->id, $conversation->fresh()->seconde_user_id);
        }
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

    private function createPet(User $user): Pet
    {
        $species = Species::create([
            'name' => 'Dog',
            'description' => 'Test species',
        ]);

        $race = Race::create([
            'species_id' => $species->id,
            'name' => 'Mixed',
        ]);

        return Pet::create([
            'user_id' => $user->id,
            'species_id' => $species->id,
            'race_id' => $race->id,
            'name' => 'Nala',
            'age' => 3,
            'sexe' => 1,
            'color' => 'brown',
            'images' => [],
            'about' => 'Test pet',
        ]);
    }
}
