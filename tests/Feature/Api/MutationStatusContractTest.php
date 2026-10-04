<?php

namespace Tests\Feature\Api;

use App\Models\Pet;
use App\Models\Race;
use App\Models\Species;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class MutationStatusContractTest extends TestCase
{
    use RefreshDatabase;

    public function test_species_creation_returns_201(): void
    {
        $admin = User::withoutEvents(
            fn() => User::factory()->create([
                'is_admin' => true,
            ])
        );

        Sanctum::actingAs($admin);

        $this->postJson('/api/v.0/species', [
            'name' => 'Dog',
        ])
            ->assertCreated()
            ->assertJsonPath('success', true)
            ->assertJsonPath(
                'message',
                'Species has been created.'
            )
            ->assertJsonPath(
                'data.name',
                'Dog'
            );
    }

    public function test_species_update_returns_200(): void
    {
        $admin = User::withoutEvents(
            fn() => User::factory()->create([
                'is_admin' => true,
            ])
        );

        $species = Species::create([
            'name' => 'Dog',
        ]);

        Sanctum::actingAs($admin);

        $this->putJson(
            '/api/v.0/species/' . $species->id,
            [
                'name' => 'Canine',
            ]
        )
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath(
                'data.name',
                'Canine'
            );
    }

    public function test_location_creation_returns_201(): void
    {
        $user = User::withoutEvents(
            fn() => User::factory()->create()
        );

        Sanctum::actingAs($user);

        $this->postJson('/api/v.0/locations', [
            'latitude' => 31.6295,
            'longitude' => -7.9811,
        ])
            ->assertCreated()
            ->assertJsonPath('success', true)
            ->assertJsonPath(
                'message',
                'Location has been created.'
            )
            ->assertJsonPath(
                'data.user_id',
                $user->id
            );
    }

    public function test_block_creation_returns_201(): void
    {
        $user = User::withoutEvents(
            fn() => User::factory()->create()
        );

        $target = User::withoutEvents(
            fn() => User::factory()->create()
        );

        Sanctum::actingAs($user);

        $this->postJson('/api/v.0/blocks', [
            'to' => $target->id,
            'cause' => 1,
            'why' => 'Test',
        ])
            ->assertCreated()
            ->assertJsonPath('success', true)
            ->assertJsonPath(
                'data.from_user_id',
                $user->id
            )
            ->assertJsonPath(
                'data.to_user_id',
                $target->id
            );
    }

    public function test_species_delete_returns_standard_200_envelope(): void
    {
        $admin = User::withoutEvents(
            fn() => User::factory()->create([
                'is_admin' => true,
            ])
        );

        $species = Species::create([
            'name' => 'Dog',
        ]);

        Sanctum::actingAs($admin);

        $this->deleteJson(
            '/api/v.0/species/' . $species->id
        )
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath(
                'message',
                'Species has been deleted successfully.'
            );
    }

    public function test_species_restore_returns_standard_200_envelope(): void
    {
        $admin = User::withoutEvents(
            fn() => User::factory()->create([
                'is_admin' => true,
            ])
        );

        $species = Species::create([
            'name' => 'Dog',
        ]);

        $species->delete();

        Sanctum::actingAs($admin);

        $this->putJson(
            '/api/v.0/species/restore/' . $species->id
        )
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath(
                'message',
                'Species has been restored successfully.'
            );
    }

    public function test_like_creation_returns_201(): void
    {
        [$user, $pet] = $this->createUserWithPet('Nala');
        [, $targetPet] = $this->createUserWithPet('Milo');

        Sanctum::actingAs($user);

        $this->postJson('/api/v.0/likes', [
            'from' => $pet->id,
            'to' => $targetPet->id,
        ])
            ->assertCreated()
            ->assertJsonPath('success', true)
            ->assertJsonPath(
                'data.from_pet_id',
                $pet->id
            )
            ->assertJsonPath(
                'data.to_pet_id',
                $targetPet->id
            );
    }

    public function test_dislike_creation_returns_201(): void
    {
        [$user, $pet] = $this->createUserWithPet('Nala');
        [, $targetPet] = $this->createUserWithPet('Milo');

        Sanctum::actingAs($user);

        $this->postJson('/api/v.0/dislikes', [
            'from' => $pet->id,
            'to' => $targetPet->id,
        ])
            ->assertCreated()
            ->assertJsonPath('success', true)
            ->assertJsonPath(
                'data.from_pet_id',
                $pet->id
            )
            ->assertJsonPath(
                'data.to_pet_id',
                $targetPet->id
            );
    }

    private function createUserWithPet(string $name): array
    {
        $user = User::withoutEvents(
            fn() => User::factory()->create()
        );

        $species = Species::create([
            'name' => 'Species-' . $user->id,
            'description' => 'Test species',
        ]);

        $race = Race::create([
            'species_id' => $species->id,
            'name' => 'Race-' . $user->id,
        ]);

        $pet = Pet::withoutEvents(
            fn() => Pet::create([
                'user_id' => $user->id,
                'species_id' => $species->id,
                'race_id' => $race->id,
                'name' => $name,
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
