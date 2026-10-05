<?php

namespace Tests\Feature\Api;

use App\Models\Location;
use App\Models\Pet;
use App\Models\Race;
use App\Models\Species;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class DiscoveryFilterContractTest extends TestCase
{
    use RefreshDatabase;

    public function test_species_filter_limits_the_persisted_result_set(): void
    {
        [$requester] = $this->createPetAt('Requester', 'Dog', 'Mixed');

        [$dogOwner, $dogPet] =
            $this->createPetAt('Dog result', 'Dog', 'Labrador');

        $this->createPetAt('Cat result', 'Cat', 'Domestic');

        Sanctum::actingAs($requester);

        $this->postJson('/api/v.0/locations/nears', [
            'species_id' => $dogPet->species_id,
        ])
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('meta.total', 1)
            ->assertJsonPath('data.0.owner.id', $dogOwner->id)
            ->assertJsonPath('data.0.pet.id', $dogPet->id);
    }

    public function test_race_filter_limits_the_persisted_result_set(): void
    {
        [$requester] = $this->createPetAt('Requester', 'Dog', 'Mixed');

        [, $labrador, $labradorRace] =
            $this->createPetAt('Labrador result', 'Dog', 'Labrador');

        $this->createPetAt('Golden result', 'Dog', 'Golden');

        Sanctum::actingAs($requester);

        $this->postJson('/api/v.0/locations/nears', [
            'species_id' => $labrador->species_id,
            'race_id' => $labradorRace->id,
        ])
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('meta.total', 1)
            ->assertJsonPath('data.0.pet.id', $labrador->id)
            ->assertJsonPath(
                'data.0.pet.race.id',
                $labradorRace->id
            );
    }

    public function test_distance_filter_changes_the_result_set(): void
    {
        [$requester] = $this->createPetAt(
            'Requester',
            'Dog',
            'Mixed',
            31.6295,
            -7.9811
        );

        $this->createPetAt(
            'Near',
            'Dog',
            'Labrador',
            31.6305,
            -7.9811
        );

        $this->createPetAt(
            'Far',
            'Dog',
            'Golden',
            31.7195,
            -7.9811
        );

        Sanctum::actingAs($requester);

        $this->postJson('/api/v.0/locations/nears', [
            'radius_km' => 5,
        ])
            ->assertOk()
            ->assertJsonPath('meta.total', 1)
            ->assertJsonPath('data.0.pet.name', 'Near');

        $this->postJson('/api/v.0/locations/nears', [
            'radius_km' => 25,
        ])
            ->assertOk()
            ->assertJsonPath('meta.total', 2);
    }

    public function test_mismatched_species_and_race_are_rejected(): void
    {
        [$requester] = $this->createPetAt('Requester', 'Dog', 'Mixed');

        [, $dog] =
            $this->createPetAt('Dog result', 'Dog', 'Labrador');

        [, , $catRace] =
            $this->createPetAt('Cat result', 'Cat', 'Domestic');

        Sanctum::actingAs($requester);

        $this->postJson('/api/v.0/locations/nears', [
            'species_id' => $dog->species_id,
            'race_id' => $catRace->id,
        ])
            ->assertUnprocessable()
            ->assertJsonPath('success', false)
            ->assertJsonStructure([
                'errors' => ['race_id'],
            ]);
    }

    public function test_unknown_taxonomy_filters_are_rejected(): void
    {
        [$requester] = $this->createPetAt('Requester', 'Dog', 'Mixed');

        Sanctum::actingAs($requester);

        $this->postJson('/api/v.0/locations/nears', [
            'species_id' => 999999,
            'race_id' => 999999,
        ])
            ->assertUnprocessable()
            ->assertJsonPath('success', false)
            ->assertJsonStructure([
                'errors' => [
                    'species_id',
                    'race_id',
                ],
            ]);
    }

    private function createPetAt(
        string $petName,
        string $speciesName,
        string $raceName,
        float $latitude = 31.6295,
        float $longitude = -7.9811,
    ): array {
        $user = User::withoutEvents(
            fn () => User::factory()->create([
                'name' => 'Owner of ' . $petName,
            ])
        );

        $species = Species::firstOrCreate([
            'name' => $speciesName,
        ]);

        $race = Race::firstOrCreate([
            'species_id' => $species->id,
            'name' => $raceName,
        ]);

        $pet = Pet::withoutEvents(
            fn () => Pet::create([
                'user_id' => $user->id,
                'species_id' => $species->id,
                'race_id' => $race->id,
                'name' => $petName,
                'age' => 3,
                'sexe' => null,
                'color' => null,
                'images' => [],
                'about' => null,
            ])
        );

        Location::create([
            'user_id' => $user->id,
            'latitude' => $latitude,
            'longitude' => $longitude,
        ]);

        return [
            $user,
            $pet,
            $race,
            $species,
        ];
    }
}
