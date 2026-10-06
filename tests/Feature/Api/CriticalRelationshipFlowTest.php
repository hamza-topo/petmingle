<?php

namespace Tests\Feature\Api;

use App\Events\MatchEvent;
use App\Mail\ItsAMatch;
use App\Models\Pet;
use App\Models\Race;
use App\Models\Species;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTruncation;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Mail;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CriticalRelationshipFlowTest extends TestCase
{
    use DatabaseTruncation;

    public function test_reciprocal_likes_create_match_and_dispatch_notifications_end_to_end(): void
    {
        Event::fake([
            MatchEvent::class,
        ]);

        Mail::fake();

        [$firstUser, $firstPet] =
            $this->createUserWithPet('Nala');

        [$secondUser, $secondPet] =
            $this->createUserWithPet('Milo');

        Sanctum::actingAs($firstUser);

        $this->postJson('/api/v.0/likes', [
            'to_pet_id' => $secondPet->id,
        ])
            ->assertOk()
            ->assertJsonPath(
                'data.from_pet_id',
                $firstPet->id
            )
            ->assertJsonPath(
                'data.to_pet_id',
                $secondPet->id
            )
            ->assertJsonPath(
                'data.interaction',
                'liked'
            );

        $this->assertDatabaseMissing('matches', [
            'from' => $firstPet->id,
            'to' => $secondPet->id,
        ]);

        $this->assertDatabaseMissing('matches', [
            'from' => $secondPet->id,
            'to' => $firstPet->id,
        ]);

        Event::assertNotDispatched(
            MatchEvent::class
        );
        Mail::assertNothingQueued();

        Sanctum::actingAs($secondUser);

        $this->postJson('/api/v.0/likes', [
            'to_pet_id' => $firstPet->id,
        ])
            ->assertOk()
            ->assertJsonPath(
                'data.from_pet_id',
                $secondPet->id
            )
            ->assertJsonPath(
                'data.to_pet_id',
                $firstPet->id
            );

        $this->assertDatabaseHas('likes', [
            'from' => $firstPet->id,
            'to' => $secondPet->id,
            'deleted_at' => null,
        ]);

        $this->assertDatabaseHas('likes', [
            'from' => $secondPet->id,
            'to' => $firstPet->id,
            'deleted_at' => null,
        ]);

        $this->assertDatabaseHas('matches', [
            'from' => $firstPet->id,
            'to' => $secondPet->id,
            'deleted_at' => null,
        ]);

        $this->assertDatabaseHas('matches', [
            'from' => $secondPet->id,
            'to' => $firstPet->id,
            'deleted_at' => null,
        ]);

        Event::assertDispatchedTimes(
            MatchEvent::class,
            1
        );

        Mail::assertQueued(
            ItsAMatch::class,
            2
        );

        Sanctum::actingAs($firstUser);

        $this->getJson('/api/v.0/matches')
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('meta.total', 1)
            ->assertJsonPath(
                'data.0.from_pet_id',
                $firstPet->id
            )
            ->assertJsonPath(
                'data.0.to_pet_id',
                $secondPet->id
            );
    }

    private function createUserWithPet(
        string $petName
    ): array {
        $user = User::withoutEvents(
            fn () => User::factory()->create()
        );

        $species = Species::withoutEvents(
            fn () => Species::create([
                'name' =>
                    'Species-' . $user->id,
                'description' =>
                    'Critical flow species',
            ])
        );

        $race = Race::withoutEvents(
            fn () => Race::create([
                'species_id' => $species->id,
                'name' =>
                    'Race-' . $user->id,
            ])
        );

        $pet = Pet::withoutEvents(
            fn () => Pet::create([
                'user_id' => $user->id,
                'species_id' => $species->id,
                'race_id' => $race->id,
                'name' => $petName,
                'age' => 3,
                'sexe' => 1,
                'color' => 'brown',
                'images' => [],
                'about' =>
                    'Critical flow pet',
            ])
        );

        return [$user, $pet];
    }
}
