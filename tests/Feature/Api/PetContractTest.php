<?php

namespace Tests\Feature\Api;

use App\Models\Pet;
use App\Models\Race;
use App\Models\Species;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class PetContractTest extends TestCase
{
    use RefreshDatabase;

    public function test_pet_index_uses_standard_api_envelope(): void
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $pet = $this->createPet($user);

        $this->getJson('/api/v.0/pets')
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('message', 'List of pets.')
            ->assertJsonPath('data.0.id', $pet->id)
            ->assertJsonPath('data.0.user_id', $user->id)
            ->assertJsonPath('data.0.name', 'Nala')
            ->assertJsonPath('data.0.images', []);
    }

    public function test_pet_show_uses_standard_api_envelope(): void
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $pet = $this->createPet($user);

        $this->getJson('/api/v.0/pets/'.$pet->id)
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('message', 'Pet has been found.')
            ->assertJsonPath('data.id', $pet->id)
            ->assertJsonPath('data.user_id', $user->id)
            ->assertJsonPath(
                'data.species_id',
                $pet->species_id
            )
            ->assertJsonPath(
                'data.race_id',
                $pet->race_id
            );
    }

    public function test_missing_pet_returns_standard_404(): void
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $this->getJson('/api/v.0/pets/999999')
            ->assertNotFound()
            ->assertExactJson([
                'success' => false,
                'message' => 'Resource not found.',
            ]);
    }

    private function createPet(User $user): Pet
    {
        $species = Species::create([
            'name' => 'Dog',
            'description' => 'Dogs',
        ]);

        $race = Race::create([
            'species_id' => $species->id,
            'name' => 'Mixed',
        ]);

        return Pet::create([
            'user_id' => $user->id,
            'species_id' => $species->id,
            'race_id' => $race->id,
            'name' => 'Nala',
            'age' => 3,
            'sexe' => 1,
            'color' => 'brown',
            'images' => [],
            'about' => 'Friendly dog',
        ]);
    }
}
