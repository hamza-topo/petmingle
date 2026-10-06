<?php

namespace Tests\Feature\Api;

use App\Models\Like;
use App\Models\MatchTable;
use App\Models\Pet;
use App\Models\Race;
use App\Models\Species;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class PetProfileStatisticsTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_can_load_persisted_profile_statistics(): void
    {
        [$owner, $pet] = $this->createUserWithPet('Milo');
        [, $firstTarget] = $this->createUserWithPet('Luna');
        [, $secondTarget] = $this->createUserWithPet('Toby');

        MatchTable::withoutEvents(
            fn () => MatchTable::create([
                'from' => $pet->id,
                'to' => $firstTarget->id,
            ])
        );

        // MatchService stores the reciprocal row as well.
        // It must not double the current pet's count.
        MatchTable::withoutEvents(
            fn () => MatchTable::create([
                'from' => $firstTarget->id,
                'to' => $pet->id,
            ])
        );

        Like::withoutEvents(
            fn () => Like::create([
                'from' => $pet->id,
                'to' => $firstTarget->id,
            ])
        );

        Like::withoutEvents(
            fn () => Like::create([
                'from' => $pet->id,
                'to' => $secondTarget->id,
            ])
        );

        Sanctum::actingAs($owner);

        $this->getJson(
            "/api/v.0/pets/{$pet->id}/statistics"
        )
            ->assertOk()
            ->assertExactJson([
                'success' => true,
                'message' => 'Pet profile statistics.',
                'data' => [
                    'matches' => 1,
                    'likes_sent' => 2,
                ],
            ]);
    }

    public function test_empty_profile_statistics_return_zeroes(): void
    {
        [$owner, $pet] = $this->createUserWithPet();

        Sanctum::actingAs($owner);

        $this->getJson(
            "/api/v.0/pets/{$pet->id}/statistics"
        )
            ->assertOk()
            ->assertJsonPath('data.matches', 0)
            ->assertJsonPath('data.likes_sent', 0);
    }

    public function test_user_cannot_load_another_pets_statistics(): void
    {
        [$owner] = $this->createUserWithPet('Owner pet');
        [, $otherPet] = $this->createUserWithPet('Other pet');

        Sanctum::actingAs($owner);

        $this->getJson(
            "/api/v.0/pets/{$otherPet->id}/statistics"
        )->assertForbidden();
    }

    private function createUserWithPet(
        string $petName = 'Nala'
    ): array {
        $user = User::withoutEvents(
            fn () => User::factory()->create()
        );

        $species = Species::create([
            'name' => 'Species-'.$user->id,
        ]);

        $race = Race::create([
            'species_id' => $species->id,
            'name' => 'Race-'.$user->id,
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
                'images' => [],
                'about' => 'Test pet',
            ])
        );

        return [$user, $pet];
    }
}
