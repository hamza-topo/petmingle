<?php

namespace Tests\Feature\Api;

use App\Models\Location;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class LocationAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_can_view_own_location(): void
    {
        $user = User::factory()->create();

        $location = Location::create([
            'user_id' => $user->id,
            'latitude' => 31.6295,
            'longitude' => -7.9811,
        ]);

        Sanctum::actingAs($user);

        $this->getJson("/api/v.0/locations/{$location->id}")
            ->assertOk();
    }

    public function test_user_cannot_view_another_users_location(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();

        $location = Location::create([
            'user_id' => $owner->id,
            'latitude' => 31.6295,
            'longitude' => -7.9811,
        ]);

        Sanctum::actingAs($otherUser);

        $this->getJson("/api/v.0/locations/{$location->id}")
            ->assertForbidden();
    }

    public function test_owner_can_delete_own_location(): void
    {
        $user = User::factory()->create();

        $location = Location::create([
            'user_id' => $user->id,
            'latitude' => 31.6295,
            'longitude' => -7.9811,
        ]);

        Sanctum::actingAs($user);

        $this->deleteJson("/api/v.0/locations/{$location->id}")
            ->assertOk();

        $this->assertSoftDeleted('locations', [
            'id' => $location->id,
        ]);
    }

    public function test_user_cannot_delete_another_users_location(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();

        $location = Location::create([
            'user_id' => $owner->id,
            'latitude' => 31.6295,
            'longitude' => -7.9811,
        ]);

        Sanctum::actingAs($otherUser);

        $this->deleteJson("/api/v.0/locations/{$location->id}")
            ->assertForbidden();

        $this->assertDatabaseHas('locations', [
            'id' => $location->id,
            'deleted_at' => null,
        ]);
    }

    public function test_owner_can_restore_own_location(): void
    {
        $user = User::factory()->create();

        $location = Location::create([
            'user_id' => $user->id,
            'latitude' => 31.6295,
            'longitude' => -7.9811,
        ]);

        $location->delete();

        Sanctum::actingAs($user);

        $this->putJson("/api/v.0/locations/restore/{$location->id}")
            ->assertOk();

        $this->assertDatabaseHas('locations', [
            'id' => $location->id,
            'deleted_at' => null,
        ]);
    }

    public function test_user_cannot_restore_another_users_location(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();

        $location = Location::create([
            'user_id' => $owner->id,
            'latitude' => 31.6295,
            'longitude' => -7.9811,
        ]);

        $location->delete();

        Sanctum::actingAs($otherUser);

        $this->putJson("/api/v.0/locations/restore/{$location->id}")
            ->assertForbidden();

        $this->assertSoftDeleted('locations', [
            'id' => $location->id,
        ]);
    }

    public function test_authenticated_user_can_create_location_without_user_id(): void
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $this->postJson('/api/v.0/locations', [
            'latitude' => 31.6295,
            'longitude' => -7.9811,
        ])->assertOk();

        $this->assertDatabaseHas('locations', [
            'user_id' => $user->id,
        ]);
    }

    public function test_client_cannot_spoof_location_user_id(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        Sanctum::actingAs($user);

        $this->postJson('/api/v.0/locations', [
            'user_id' => $otherUser->id,
            'latitude' => 31.6295,
            'longitude' => -7.9811,
        ])->assertOk();

        $this->assertDatabaseHas('locations', [
            'user_id' => $user->id,
        ]);

        $this->assertDatabaseMissing('locations', [
            'user_id' => $otherUser->id,
        ]);
    }

    public function test_location_index_only_returns_authenticated_users_locations(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $ownLocation = Location::create([
            'user_id' => $user->id,
            'latitude' => 31.6295,
            'longitude' => -7.9811,
        ]);

        $otherLocation = Location::create([
            'user_id' => $otherUser->id,
            'latitude' => 33.5731,
            'longitude' => -7.5898,
        ]);

        Sanctum::actingAs($user);

        $response = $this->getJson('/api/v.0/locations')
            ->assertOk();

        $response->assertJsonFragment([
            'id' => $ownLocation->id,
            'user_id' => $user->id,
        ]);

        $response->assertJsonMissing([
            'id' => $otherLocation->id,
            'user_id' => $otherUser->id,
        ]);
    }
}
