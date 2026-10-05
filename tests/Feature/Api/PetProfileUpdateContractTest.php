<?php

namespace Tests\Feature\Api;

use App\Models\Pet;
use App\Models\Race;
use App\Models\Species;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class PetProfileUpdateContractTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_can_update_supported_profile_fields(): void
    {
        $owner = User::factory()->create();
        [$dog, $dogRace] = $this->taxonomy('Dog', 'Mixed');
        [$cat, $catRace] = $this->taxonomy('Cat', 'Domestic');

        $pet = Pet::create([
            'user_id' => $owner->id,
            'species_id' => $dog->id,
            'race_id' => $dogRace->id,
            'name' => 'Nala',
            'age' => 3,
            'sexe' => 1,
            'color' => 'brown',
            'images' => [],
            'about' => 'Old biography',
        ]);

        Sanctum::actingAs($owner);

        $this->putJson("/api/v.0/pets/{$pet->id}", [
            'species_id' => $cat->id,
            'race_id' => $catRace->id,
            'name' => '  Milo  ',
            'age' => 5,
            'about' => 'Updated biography',
        ])
            ->assertOk()
            ->assertJsonPath('data.species_id', $cat->id)
            ->assertJsonPath('data.race_id', $catRace->id)
            ->assertJsonPath('data.name', 'Milo')
            ->assertJsonPath('data.age', 5)
            ->assertJsonPath(
                'data.about',
                'Updated biography'
            );

        $this->assertDatabaseHas('pets', [
            'id' => $pet->id,
            'user_id' => $owner->id,
            'species_id' => $cat->id,
            'race_id' => $catRace->id,
            'name' => 'Milo',
            'age' => 5,
            'about' => 'Updated biography',
        ]);
    }

    public function test_update_rejects_race_from_another_species(): void
    {
        $owner = User::factory()->create();
        [$dog, $dogRace] = $this->taxonomy('Dog', 'Mixed');
        [, $catRace] = $this->taxonomy('Cat', 'Domestic');

        $pet = Pet::create([
            'user_id' => $owner->id,
            'species_id' => $dog->id,
            'race_id' => $dogRace->id,
            'name' => 'Nala',
            'age' => 3,
            'sexe' => 1,
            'color' => 'brown',
            'images' => [],
            'about' => 'Biography',
        ]);

        Sanctum::actingAs($owner);

        $this->putJson("/api/v.0/pets/{$pet->id}", [
            'species_id' => $dog->id,
            'race_id' => $catRace->id,
        ])
            ->assertUnprocessable()
            ->assertJsonPath('success', false)
            ->assertJsonStructure([
                'errors' => ['race_id'],
            ]);

        $this->assertSame(
            $dogRace->id,
            $pet->fresh()->race_id
        );
    }

    public function test_partial_non_taxonomy_update_remains_supported(): void
    {
        $owner = User::factory()->create();
        [$species, $race] = $this->taxonomy('Dog', 'Mixed');

        $pet = Pet::create([
            'user_id' => $owner->id,
            'species_id' => $species->id,
            'race_id' => $race->id,
            'name' => 'Nala',
            'age' => 3,
            'sexe' => 1,
            'color' => 'brown',
            'images' => [],
            'about' => 'Biography',
        ]);

        Sanctum::actingAs($owner);

        $this->putJson("/api/v.0/pets/{$pet->id}", [
            'name' => 'M',
        ])
            ->assertOk()
            ->assertJsonPath('data.name', 'M');
    }

    private function taxonomy(
        string $speciesName,
        string $raceName,
    ): array {
        $species = Species::create([
            'name' => $speciesName,
            'description' => $speciesName,
        ]);

        $race = Race::create([
            'species_id' => $species->id,
            'name' => $raceName,
        ]);

        return [$species, $race];
    }
}
