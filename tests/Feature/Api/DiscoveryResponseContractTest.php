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

class DiscoveryResponseContractTest extends TestCase
{
    use RefreshDatabase;

    public function test_nearby_response_exposes_stable_card_fields_and_identifiers(): void
    {
        [$requester] = $this->createUserWithPetAndLocation(
            'Requester',
            31.6295,
            -7.9811
        );

        [$owner, $pet, $race] =
            $this->createUserWithPetAndLocation(
                'Milo',
                31.6305,
                -7.9811,
                [
                    'age' => 4,
                    'sexe' => 1,
                    'images' => [
                        'pets/milo.jpg',
                        '',
                    ],
                    'about' => 'Friendly and curious.',
                ]
            );

        Sanctum::actingAs($requester);

        $response = $this->postJson(
            '/api/v.0/locations/nears',
            [
                'radius_km' => 5,
                'page' => 1,
                'per_page' => 24,
            ]
        )
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('message', 'Nearby pets.')
            ->assertJsonPath('data.0.owner.id', $owner->id)
            ->assertJsonPath(
                'data.0.owner.name',
                $owner->name
            )
            ->assertJsonPath('data.0.pet.id', $pet->id)
            ->assertJsonPath(
                'data.0.pet.owner_id',
                $owner->id
            )
            ->assertJsonPath(
                'data.0.pet.species_id',
                $pet->species_id
            )
            ->assertJsonPath('data.0.pet.name', 'Milo')
            ->assertJsonPath('data.0.pet.age_years', 4)
            ->assertJsonPath('data.0.pet.sex', 1)
            ->assertJsonPath(
                'data.0.pet.race.id',
                $race->id
            )
            ->assertJsonPath(
                'data.0.pet.race.species_id',
                $race->species_id
            )
            ->assertJsonPath(
                'data.0.pet.race.name',
                $race->name
            )
            ->assertJsonPath(
                'data.0.pet.images',
                ['pets/milo.jpg']
            )
            ->assertJsonPath(
                'data.0.pet.about',
                'Friendly and curious.'
            )
            ->assertJsonPath('data.0.distance_km', 0.11)
            ->assertJsonPath('data.0.is_new', true)
            ->assertJsonPath('meta.current_page', 1)
            ->assertJsonPath('meta.last_page', 1)
            ->assertJsonPath('meta.per_page', 24)
            ->assertJsonPath('meta.total', 1)
            ->assertJsonStructure([
                'links' => [
                    'first',
                    'last',
                    'prev',
                    'next',
                ],
            ]);

        $item = $response->json('data.0');

        $this->assertArrayNotHasKey(
            'verified',
            $item['pet']
        );
        $this->assertArrayNotHasKey(
            'traits',
            $item['pet']
        );
        $this->assertArrayNotHasKey(
            'companion',
            $item['pet']
        );
        $this->assertArrayNotHasKey(
            'location',
            $item
        );
        $this->assertArrayNotHasKey(
            'latitude',
            $item
        );
        $this->assertArrayNotHasKey(
            'longitude',
            $item
        );
        $this->assertIsFloat(
            $item['distance_km']
        );
    }

    public function test_nearby_response_is_paginated_consistently(): void
    {
        [$requester] = $this->createUserWithPetAndLocation(
            'Requester',
            31.6295,
            -7.9811
        );

        foreach (['Alpha', 'Bravo', 'Charlie'] as $name) {
            $this->createUserWithPetAndLocation(
                $name,
                31.6295,
                -7.9811
            );
        }

        Sanctum::actingAs($requester);

        $this->postJson(
            '/api/v.0/locations/nears',
            [
                'page' => 1,
                'per_page' => 2,
            ]
        )
            ->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJsonPath('meta.current_page', 1)
            ->assertJsonPath('meta.last_page', 2)
            ->assertJsonPath('meta.per_page', 2)
            ->assertJsonPath('meta.total', 3);

        $this->postJson(
            '/api/v.0/locations/nears',
            [
                'page' => 2,
                'per_page' => 2,
            ]
        )
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('meta.current_page', 2)
            ->assertJsonPath('meta.last_page', 2)
            ->assertJsonPath('meta.total', 3);
    }

    public function test_empty_nearby_results_keep_the_same_paginated_shape(): void
    {
        [$requester] = $this->createUserWithPetAndLocation(
            'Requester',
            31.6295,
            -7.9811
        );

        Sanctum::actingAs($requester);

        $this->postJson('/api/v.0/locations/nears')
            ->assertOk()
            ->assertJsonPath('data', [])
            ->assertJsonPath('meta.current_page', 1)
            ->assertJsonPath('meta.last_page', 1)
            ->assertJsonPath('meta.per_page', 24)
            ->assertJsonPath('meta.total', 0)
            ->assertJsonStructure([
                'links' => [
                    'first',
                    'last',
                    'prev',
                    'next',
                ],
            ]);
    }

    public function test_page_and_page_size_are_validated(): void
    {
        [$requester] = $this->createUserWithPetAndLocation(
            'Requester',
            31.6295,
            -7.9811
        );

        Sanctum::actingAs($requester);

        $this->postJson('/api/v.0/locations/nears', [
            'page' => 0,
            'per_page' => 51,
        ])
            ->assertUnprocessable()
            ->assertJsonPath('success', false)
            ->assertJsonPath('message', 'Validation failed.')
            ->assertJsonStructure([
                'errors' => [
                    'page',
                    'per_page',
                ],
            ]);
    }

    private function createUserWithPetAndLocation(
        string $petName,
        float $latitude,
        float $longitude,
        array $petOverrides = [],
    ): array {
        $user = User::withoutEvents(
            fn () => User::factory()->create([
                'name' => 'Owner of '.$petName,
            ])
        );

        $species = Species::create([
            'name' => 'Species-'.$user->id,
        ]);

        $race = Race::create([
            'species_id' => $species->id,
            'name' => 'Race-'.$user->id,
        ]);

        $pet = Pet::withoutEvents(
            fn () => Pet::create(array_merge([
                'user_id' => $user->id,
                'species_id' => $species->id,
                'race_id' => $race->id,
                'name' => $petName,
                'age' => 3,
                'sexe' => null,
                'color' => null,
                'images' => [],
                'about' => null,
            ], $petOverrides))
        );

        $location = Location::create([
            'user_id' => $user->id,
            'latitude' => $latitude,
            'longitude' => $longitude,
        ]);

        return [
            $user,
            $pet,
            $race,
            $location,
        ];
    }
}
