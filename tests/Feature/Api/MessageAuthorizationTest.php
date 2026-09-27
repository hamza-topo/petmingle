<?php

namespace Tests\Feature\Api;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;
use App\Events\MessageEvent;
use Illuminate\Support\Facades\Event;
use App\Models\Like;

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

        Like::create([
            'from' => $receiver->id,
            'to' => $sender->id,
        ]);

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

        Like::create([
            'from' => $receiver->id,
            'to' => $sender->id,
        ]);

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
}
