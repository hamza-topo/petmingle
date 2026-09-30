<?php

namespace Tests\Feature\Admin;

use App\Models\Pet;
use App\Models\Race;
use App\Models\Species;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PetControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_delete_pet(): void
    {
        $admin = User::factory()->create([
            'is_admin' => true,
        ]);

        $owner = User::factory()->create();

        $species = Species::create([
            'name' => 'Dog',
            'description' => 'Test species',
        ]);

        $race = Race::create([
            'species_id' => $species->id,
            'name' => 'Mixed',
        ]);

        $pet = Pet::create([
            'user_id' => $owner->id,
            'species_id' => $species->id,
            'race_id' => $race->id,
            'name' => 'Nala',
            'age' => 3,
            'sexe' => 1,
            'color' => 'brown',
            'images' => [],
            'about' => 'Test pet',
        ]);

        $this->actingAs($admin);

        $this->delete("/admin/pets/{$pet->id}")
            ->assertRedirect(route('admin.pets.index'));

        $this->assertSoftDeleted('pets', [
            'id' => $pet->id,
        ]);
    }
}