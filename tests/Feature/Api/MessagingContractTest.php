<?php

namespace Tests\Feature\Api;

use App\Models\Conversation;
use App\Models\MatchTable;
use App\Models\Message;
use App\Models\Pet;
use App\Models\Race;
use App\Models\Species;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class MessagingContractTest extends TestCase
{
    use RefreshDatabase;

    public function test_conversation_list_has_stable_participant_and_summary_contract(): void
    {
        [$currentUser, $currentPet] = $this->userWithPet(
            'Current',
            'Nala'
        );
        [$firstUser, $firstPet] = $this->userWithPet(
            'First',
            'Milo'
        );
        [$secondUser, $secondPet] = $this->userWithPet(
            'Second',
            'Luna'
        );

        $firstConversation = Conversation::create([
            'first_user_id' => $currentUser->id,
            'seconde_user_id' => $firstUser->id,
        ]);

        Message::withoutEvents(fn () => Message::create([
            'conversation_id' => $firstConversation->id,
            'sender_id' => $firstUser->id,
            'receiver_id' => $currentUser->id,
            'content' => 'Older conversation',
            'is_seen' => false,
        ]));

        $secondConversation = Conversation::create([
            'first_user_id' => $currentUser->id,
            'seconde_user_id' => $secondUser->id,
        ]);

        $latest = Message::withoutEvents(
            fn () => Message::create([
                'conversation_id' => $secondConversation->id,
                'sender_id' => $secondUser->id,
                'receiver_id' => $currentUser->id,
                'content' => 'Latest incoming message',
                'is_seen' => false,
            ])
        );

        Message::withoutEvents(fn () => Message::create([
            'conversation_id' => $secondConversation->id,
            'sender_id' => $currentUser->id,
            'receiver_id' => $secondUser->id,
            'content' => 'Latest outgoing message',
            'is_seen' => true,
        ]));

        $outsider = User::factory()->create();
        $other = User::factory()->create();

        Conversation::create([
            'first_user_id' => $outsider->id,
            'seconde_user_id' => $other->id,
        ]);

        Sanctum::actingAs($currentUser);

        $response = $this->getJson(
            '/api/v.0/conversations?per_page=10'
        )
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath(
                'message',
                'List of conversations.'
            )
            ->assertJsonPath('meta.total', 2)
            ->assertJsonPath('meta.per_page', 10)
            ->assertJsonPath(
                'data.0.id',
                $secondConversation->id
            )
            ->assertJsonPath(
                'data.0.current_user_id',
                $currentUser->id
            )
            ->assertJsonPath(
                'data.0.participants.0.user_id',
                $currentUser->id
            )
            ->assertJsonPath(
                'data.0.participants.0.pet.id',
                $currentPet->id
            )
            ->assertJsonPath(
                'data.0.participants.1.user_id',
                $secondUser->id
            )
            ->assertJsonPath(
                'data.0.participants.1.pet.id',
                $secondPet->id
            )
            ->assertJsonPath(
                'data.0.last_message.content',
                'Latest outgoing message'
            )
            ->assertJsonPath(
                'data.0.last_message.sender_user_id',
                $currentUser->id
            )
            ->assertJsonPath(
                'data.0.last_message.receiver_user_id',
                $secondUser->id
            )
            ->assertJsonPath(
                'data.0.unread_count',
                1
            );

        $this->assertIsString(
            $response->json(
                'data.0.last_message.created_at'
            )
        );

        $this->assertSame(
            $latest->conversation_id,
            $secondConversation->id
        );

        $this->assertSame(
            $firstPet->id,
            $response->json(
                'data.1.participants.1.pet.id'
            )
        );
    }

    public function test_thread_is_bidirectional_paginated_and_chronological_within_page(): void
    {
        [$currentUser, $currentPet] = $this->userWithPet(
            'Current',
            'Nala'
        );
        [$otherUser, $otherPet] = $this->userWithPet(
            'Other',
            'Milo'
        );

        $this->establishMatch(
            $currentPet,
            $otherPet
        );

        $conversation = Conversation::create([
            'first_user_id' => $currentUser->id,
            'seconde_user_id' => $otherUser->id,
        ]);

        $first = $this->message(
            $conversation,
            $currentUser,
            $otherUser,
            'First message',
            true
        );

        $second = $this->message(
            $conversation,
            $otherUser,
            $currentUser,
            'Second message',
            false
        );

        $third = $this->message(
            $conversation,
            $currentUser,
            $otherUser,
            'Third message',
            false
        );

        Sanctum::actingAs($currentUser);

        $response = $this->getJson(
            '/api/v.0/messages?receiver_id='
            . $otherUser->id
            . '&per_page=2&page=1'
        )
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath(
                'message',
                'Message thread.'
            )
            ->assertJsonPath('meta.total', 3)
            ->assertJsonPath('meta.per_page', 2)
            ->assertJsonPath('meta.current_page', 1)
            ->assertJsonPath(
                'data.0.id',
                $second->id
            )
            ->assertJsonPath(
                'data.0.sender_user_id',
                $otherUser->id
            )
            ->assertJsonPath(
                'data.0.receiver_user_id',
                $currentUser->id
            )
            ->assertJsonPath(
                'data.0.content',
                'Second message'
            )
            ->assertJsonPath(
                'data.0.is_seen',
                false
            )
            ->assertJsonPath(
                'data.1.id',
                $third->id
            )
            ->assertJsonPath(
                'data.1.content',
                'Third message'
            );

        $this->assertIsString(
            $response->json('data.0.created_at')
        );

        $this->getJson(
            '/api/v.0/messages?receiver_id='
            . $otherUser->id
            . '&per_page=2&page=2'
        )
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath(
                'data.0.id',
                $first->id
            )
            ->assertJsonPath(
                'data.0.content',
                'First message'
            );
    }

    public function test_thread_rejects_client_sender_scope_and_invalid_page_size(): void
    {
        [$currentUser, $currentPet] = $this->userWithPet(
            'Current',
            'Nala'
        );
        [$otherUser, $otherPet] = $this->userWithPet(
            'Other',
            'Milo'
        );

        $this->establishMatch(
            $currentPet,
            $otherPet
        );

        Sanctum::actingAs($currentUser);

        $this->getJson(
            '/api/v.0/messages?receiver_id='
            . $otherUser->id
            . '&sender_id='
            . $otherUser->id
            . '&per_page=100'
        )
            ->assertUnprocessable()
            ->assertJsonStructure([
                'errors' => [
                    'sender_id',
                    'per_page',
                ],
            ]);
    }

    private function message(
        Conversation $conversation,
        User $sender,
        User $receiver,
        string $content,
        bool $seen
    ): Message {
        usleep(1000);

        return Message::withoutEvents(
            fn () => Message::create([
                'conversation_id' => $conversation->id,
                'sender_id' => $sender->id,
                'receiver_id' => $receiver->id,
                'content' => $content,
                'is_seen' => $seen,
            ])
        );
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

    private function userWithPet(
        string $userName,
        string $petName
    ): array {
        $user = User::withoutEvents(
            fn () => User::factory()->create([
                'name' => $userName,
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
                'name' => $petName,
                'age' => 3,
                'sexe' => 1,
                'color' => 'brown',
                'images' => [
                    'pets/' . strtolower($petName) . '.jpg',
                ],
                'about' => 'Test pet',
            ])
        );

        return [$user, $pet];
    }
}
