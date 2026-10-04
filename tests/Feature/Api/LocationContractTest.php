<?php

namespace Tests\Feature\Api;

use App\Models\Location;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class LocationContractTest extends TestCase
{
    use RefreshDatabase;

    public function test_location_index_uses_standard_api_envelope(): void
    {
        $user = User::factory()->create();

        $location = Location::create([
            'user_id' => $user->id,
            'latitude' => 31.6295,
            'longitude' => -7.9811,
        ]);

        Sanctum::actingAs($user);

        $this->getJson('/api/v.0/locations')
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('message', 'List of locations.')
            ->assertJsonPath(
                'data.0.id',
                $location->id
            )
            ->assertJsonPath(
                'data.0.user_id',
                $user->id
            )
            ->assertJsonPath(
                'data.0.latitude',
                31.6295
            )
            ->assertJsonPath(
                'data.0.longitude',
                -7.9811
            );
    }

    public function test_location_show_uses_standard_api_envelope(): void
    {
        $user = User::factory()->create();

        $location = Location::create([
            'user_id' => $user->id,
            'latitude' => 31.6295,
            'longitude' => -7.9811,
        ]);

        Sanctum::actingAs($user);

        $this->getJson(
            '/api/v.0/locations/' . $location->id
        )
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath(
                'message',
                'Location has been found.'
            )
            ->assertJsonPath(
                'data.id',
                $location->id
            )
            ->assertJsonPath(
                'data.latitude',
                31.6295
            );
    }

    public function test_missing_location_returns_standard_404(): void
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $this->getJson('/api/v.0/locations/999999')
            ->assertNotFound()
            ->assertExactJson([
                'success' => false,
                'message' => 'Resource not found.',
            ]);
    }
}