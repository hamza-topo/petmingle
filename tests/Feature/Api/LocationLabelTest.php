<?php

namespace Tests\Feature\Api;

use App\Models\Location;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class LocationLabelTest extends TestCase
{
    use RefreshDatabase;

    public function test_selected_city_survives_create_update_and_reload(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/v.0/locations', [
            'latitude' => 33.5731,
            'longitude' => -7.5898,
            'label' => 'Casablanca, Morocco',
        ])->assertCreated()->assertJsonPath('data.label', 'Casablanca, Morocco');

        $id = $response->json('data.id');
        $this->getJson('/api/v.0/locations')->assertOk()->assertJsonPath('data.0.label', 'Casablanca, Morocco');
        $this->putJson("/api/v.0/locations/{$id}", [
            'latitude' => 31.6295,
            'longitude' => -7.9811,
            'label' => 'Marrakech, Morocco',
        ])->assertOk()->assertJsonPath('data.label', 'Marrakech, Morocco');
        $this->getJson("/api/v.0/locations/{$id}")->assertOk()->assertJsonPath('data.label', 'Marrakech, Morocco');
    }

    public function test_legacy_coordinate_update_clears_a_stale_city_label(): void
    {
        $user = User::factory()->create();
        $location = Location::create([
            'user_id' => $user->id,
            'latitude' => 33.5731,
            'longitude' => -7.5898,
            'label' => 'Casablanca, Morocco',
        ]);
        Sanctum::actingAs($user);

        $this->putJson("/api/v.0/locations/{$location->id}", [
            'latitude' => 31.6295,
            'longitude' => -7.9811,
        ])->assertOk()->assertJsonPath('data.label', null);
        $this->assertDatabaseHas('locations', ['id' => $location->id, 'label' => null]);
    }

    public function test_location_label_is_bounded_and_optional(): void
    {
        Sanctum::actingAs(User::factory()->create());
        $coordinates = ['latitude' => 31.6295, 'longitude' => -7.9811];
        $this->postJson('/api/v.0/locations', $coordinates + ['label' => str_repeat('a', 161)])
            ->assertUnprocessable()->assertJsonValidationErrors('label');
        $this->postJson('/api/v.0/locations', $coordinates + ['label' => null])
            ->assertCreated()->assertJsonPath('data.label', null);
    }
}
