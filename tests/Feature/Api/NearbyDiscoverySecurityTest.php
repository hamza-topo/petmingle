<?php

namespace Tests\Feature\Api;

use App\Models\Block;
use App\Models\Location;
use App\Models\Pet;
use App\Models\Race;
use App\Models\Species;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class NearbyDiscoverySecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_client_cannot_spoof_requester_or_discovery_coordinates(): void
    {
        [$user] = $this->createUserWithPetAndLocation(
            'Requester',
            31.6295,
            -7.9811
        );

        $otherUser = User::factory()->create();

        Sanctum::actingAs($user);

        $this->postJson('/api/v.0/locations/nears', [
            'user_id' => $otherUser->id,
            'latitude' => 0,
            'longitude' => 0,
            'perimetre' => 10000,
        ])
            ->assertUnprocessable()
            ->assertJsonPath('success', false)
            ->assertJsonPath('message', 'Validation failed.')
            ->assertJsonStructure([
                'errors' => [
                    'user_id',
                    'latitude',
                    'longitude',
                    'perimetre',
                ],
            ]);
    }

    public function test_radius_is_bounded_and_injection_like_values_are_rejected(): void
    {
        [$user] = $this->createUserWithPetAndLocation(
            'Requester',
            31.6295,
            -7.9811
        );

        Sanctum::actingAs($user);

        foreach ([0, 101, '5 OR 1=1'] as $radius) {
            $this->postJson('/api/v.0/locations/nears', [
                'radius_km' => $radius,
            ])
                ->assertUnprocessable()
                ->assertJsonStructure([
                    'errors' => ['radius_km'],
                ]);
        }

        $this->postJson('/api/v.0/locations/nears', [
            'radius_km' => 1,
        ])->assertOk();

        $this->postJson('/api/v.0/locations/nears', [
            'radius_km' => 100,
        ])->assertOk();
    }

    public function test_requester_without_pet_fails_safely(): void
    {
        $user = User::factory()->create();

        Location::create([
            'user_id' => $user->id,
            'latitude' => 31.6295,
            'longitude' => -7.9811,
        ]);

        Sanctum::actingAs($user);

        $this->postJson('/api/v.0/locations/nears')
            ->assertUnprocessable()
            ->assertJsonPath('success', false)
            ->assertJsonStructure([
                'errors' => ['pet'],
            ]);
    }

    public function test_requester_without_usable_location_fails_safely(): void
    {
        [$user] = $this->createUserWithPet(
            'Requester'
        );

        Location::create([
            'user_id' => $user->id,
            'latitude' => null,
            'longitude' => null,
        ]);

        Sanctum::actingAs($user);

        $this->postJson('/api/v.0/locations/nears')
            ->assertUnprocessable()
            ->assertJsonPath('success', false)
            ->assertJsonStructure([
                'errors' => ['location'],
            ]);
    }

    public function test_latest_usable_requester_location_is_used_when_newer_record_is_null(): void
    {
        [$user] = $this->createUserWithPet(
            'Requester'
        );

        Location::create([
            'user_id' => $user->id,
            'latitude' => 31.6295,
            'longitude' => -7.9811,
        ]);

        Location::create([
            'user_id' => $user->id,
            'latitude' => null,
            'longitude' => null,
        ]);

        $this->createUserWithPetAndLocation(
            'Visible nearby pet',
            31.6295,
            -7.9811
        );

        Sanctum::actingAs($user);

        $this->postJson('/api/v.0/locations/nears', [
            'radius_km' => 5,
        ])
            ->assertOk()
            ->assertJsonPath(
                'data.0.pet.name',
                'Visible nearby pet'
            );
    }

    public function test_discovery_excludes_self_blocks_and_accounts_without_usable_pet_location(): void
    {
        [$requester] = $this->createUserWithPetAndLocation(
            'Requester',
            31.6295,
            -7.9811
        );

        [$visibleUser] = $this->createUserWithPetAndLocation(
            'Visible',
            31.6295,
            -7.9811
        );

        [$blockedByRequester] =
            $this->createUserWithPetAndLocation(
                'Blocked outgoing',
                31.6295,
                -7.9811
            );

        [$blockedRequester] =
            $this->createUserWithPetAndLocation(
                'Blocked incoming',
                31.6295,
                -7.9811
            );

        Block::withoutEvents(
            fn () => Block::create([
                'from' => $requester->id,
                'to' => $blockedByRequester->id,
            ])
        );

        Block::withoutEvents(
            fn () => Block::create([
                'from' => $blockedRequester->id,
                'to' => $requester->id,
            ])
        );

        $noPetUser = User::factory()->create();

        Location::create([
            'user_id' => $noPetUser->id,
            'latitude' => 31.6295,
            'longitude' => -7.9811,
        ]);

        [, $noLocationPet] = $this->createUserWithPet(
            'No usable location'
        );

        Location::create([
            'user_id' => $noLocationPet->user_id,
            'latitude' => null,
            'longitude' => null,
        ]);

        Sanctum::actingAs($requester);

        $response = $this->postJson(
            '/api/v.0/locations/nears',
            ['radius_km' => 5]
        )->assertOk();

        $response
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.pet.name', 'Visible');

        $response->assertJsonMissing([
            'name' => 'Blocked outgoing',
        ]);

        $response->assertJsonMissing([
            'name' => 'Blocked incoming',
        ]);

        $response->assertJsonMissing([
            'name' => 'Requester',
        ]);

        $this->assertNotSame(
            $requester->id,
            $visibleUser->id
        );
    }

    public function test_legacy_filter_endpoint_cannot_bypass_nearby_security_contract(): void
    {
        [$user] = $this->createUserWithPetAndLocation(
            'Requester',
            31.6295,
            -7.9811
        );

        Sanctum::actingAs($user);

        $this->postJson('/api/v.0/locations/filters', [
            'user_id' => 999999,
            'filters' => [
                'latitude' => 0,
                'longitude' => 0,
                'perimetre' => 999999,
            ],
        ])
            ->assertUnprocessable()
            ->assertJsonPath('success', false)
            ->assertJsonStructure([
                'errors' => [
                    'user_id',
                    'filters',
                ],
            ]);
    }

    private function createUserWithPetAndLocation(
        string $petName,
        float $latitude,
        float $longitude,
    ): array {
        [$user, $pet] = $this->createUserWithPet(
            $petName
        );

        $location = Location::create([
            'user_id' => $user->id,
            'latitude' => $latitude,
            'longitude' => $longitude,
        ]);

        return [$user, $pet, $location];
    }

    private function createUserWithPet(
        string $petName,
    ): array {
        $user = User::withoutEvents(
            fn () => User::factory()->create()
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
                'images' => [],
                'about' => 'Discovery test pet',
            ])
        );

        return [$user, $pet];
    }
}
