<?php

namespace Tests\Feature\Api;

use App\Events\IsWritingEvent;
use App\Models\Block;
use App\Models\MatchTable;
use App\Models\Pet;
use App\Models\Race;
use App\Models\Species;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class RealtimeMessagingTest extends TestCase
{
    use RefreshDatabase;

    public function test_matched_sender_can_publish_typing_state_to_receiver(): void
    {
        [$sender, $senderPet] =
            $this->userWithPet('Sender');
        [$receiver, $receiverPet] =
            $this->userWithPet('Receiver');

        $this->establishMatch(
            $senderPet,
            $receiverPet
        );

        Event::fake([
            IsWritingEvent::class,
        ]);

        Sanctum::actingAs($sender);

        $this->postJson(
            '/api/v.0/messages/typing',
            [
                'receiver_id' => $receiver->id,
                'is_writing' => true,
            ]
        )
            ->assertOk()
            ->assertJsonPath(
                'data.receiver_user_id',
                $receiver->id
            )
            ->assertJsonPath(
                'data.is_writing',
                true
            );

        Event::assertDispatched(
            IsWritingEvent::class,
            function (
                IsWritingEvent $event
            ) use ($sender, $receiver) {
                return $event->broadcastWith() === [
                    'sender_user_id' => $sender->id,
                    'receiver_user_id' => $receiver->id,
                    'is_writing' => true,
                ];
            }
        );
    }

    public function test_typing_sender_identity_cannot_be_spoofed(): void
    {
        [$sender, $senderPet] =
            $this->userWithPet('Sender');
        [$receiver, $receiverPet] =
            $this->userWithPet('Receiver');

        $this->establishMatch(
            $senderPet,
            $receiverPet
        );

        Event::fake([
            IsWritingEvent::class,
        ]);

        Sanctum::actingAs($sender);

        $this->postJson(
            '/api/v.0/messages/typing',
            [
                'sender_id' => $receiver->id,
                'receiver_id' => $receiver->id,
                'is_writing' => true,
            ]
        )
            ->assertUnprocessable()
            ->assertJsonStructure([
                'errors' => ['sender_id'],
            ]);

        Event::assertNotDispatched(
            IsWritingEvent::class
        );
    }

    public function test_unmatched_or_blocked_accounts_cannot_publish_typing_state(): void
    {
        [$sender, $senderPet] =
            $this->userWithPet('Sender');
        [$receiver, $receiverPet] =
            $this->userWithPet('Receiver');

        Event::fake([
            IsWritingEvent::class,
        ]);

        Sanctum::actingAs($sender);

        $payload = [
            'receiver_id' => $receiver->id,
            'is_writing' => true,
        ];

        $this->postJson(
            '/api/v.0/messages/typing',
            $payload
        )
            ->assertUnprocessable()
            ->assertJsonStructure([
                'errors' => ['receiver_id'],
            ]);

        $this->establishMatch(
            $senderPet,
            $receiverPet
        );

        Block::withoutEvents(
            fn () => Block::create([
                'from' => $receiver->id,
                'to' => $sender->id,
            ])
        );

        $this->postJson(
            '/api/v.0/messages/typing',
            $payload
        )
            ->assertUnprocessable()
            ->assertJsonStructure([
                'errors' => ['receiver_id'],
            ]);

        Event::assertNotDispatched(
            IsWritingEvent::class
        );
    }

    private function establishMatch(
        Pet $firstPet,
        Pet $secondPet
    ): void {
        MatchTable::withoutEvents(
            function () use (
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
            }
        );
    }

    private function userWithPet(
        string $name
    ): array {
        $user = User::withoutEvents(
            fn () => User::factory()->create([
                'name' => $name,
            ])
        );

        $species = Species::withoutEvents(
            fn () => Species::create([
                'name' => 'Species-'.$user->id,
            ])
        );

        $race = Race::withoutEvents(
            fn () => Race::create([
                'species_id' => $species->id,
                'name' => 'Race-'.$user->id,
            ])
        );

        $pet = Pet::withoutEvents(
            fn () => Pet::create([
                'user_id' => $user->id,
                'species_id' => $species->id,
                'race_id' => $race->id,
                'name' => $name.' pet',
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
