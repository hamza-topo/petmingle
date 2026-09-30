<?php

namespace Tests\Feature\Api;

use App\Models\Pet;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use App\Models\Race;
use App\Models\Species;

class PetAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_cannot_create_pet_for_another_user(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $pet = $this->createPet($user);

        Sanctum::actingAs($user);

        $response = $this->postJson('/api/v.0/pets', [
            'user_id' => $otherUser->id,
            'species_id' => $pet->species_id,
            'race_id' => $pet->race_id,
            'name' => 'Nala',
            'age' => 3,
            'sexe' => 1,
            'color' => 'brown',
            'about' => 'Test pet',
            'images' => UploadedFile::fake()
                ->image('pet.jpg')
                ->size(1024),
        ]);

        $response->assertOk();

        $this->assertDatabaseHas('pets', [
            'name' => 'Nala',
            'user_id' => $user->id,
        ]);

        $this->assertDatabaseMissing('pets', [
            'name' => 'Nala',
            'user_id' => $otherUser->id,
        ]);
    }

    public function test_owner_can_restore_own_pet(): void
    {
        $user = User::factory()->create();

        $pet = $this->createPet($user);

        $pet->delete();

        Sanctum::actingAs($user);

        $this->putJson("/api/v.0/pets/restore/{$pet->id}")
            ->assertOk();

        $this->assertDatabaseHas('pets', [
            'id' => $pet->id,
            'deleted_at' => null,
        ]);
    }

    public function test_user_cannot_restore_another_users_pet(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();

        $pet = $this->createPet($owner);

        $pet->delete();

        Sanctum::actingAs($otherUser);

        $this->putJson("/api/v.0/pets/restore/{$pet->id}")
            ->assertForbidden();

        $this->assertSoftDeleted('pets', [
            'id' => $pet->id,
        ]);
    }

    public function test_owner_can_delete_own_pet(): void
    {
        $user = User::factory()->create();

        $pet = $this->createPet($user);

        Sanctum::actingAs($user);

        $this->deleteJson("/api/v.0/pets/{$pet->id}")
            ->assertOk();

        $this->assertSoftDeleted('pets', [
            'id' => $pet->id,
        ]);
    }

    public function test_user_cannot_delete_another_users_pet(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();

        $pet = $this->createPet($owner);

        Sanctum::actingAs($otherUser);

        $this->deleteJson("/api/v.0/pets/{$pet->id}")
            ->assertForbidden();

        $this->assertDatabaseHas('pets', [
            'id' => $pet->id,
            'deleted_at' => null,
        ]);
    }

    public function test_owner_can_update_own_pet(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();

        $pet = $this->createPet($owner);

        Sanctum::actingAs($owner);

        $this->putJson("/api/v.0/pets/{$pet->id}", [
            'name' => 'Milo',
            'user_id' => $otherUser->id,
        ])->assertOk();

        $this->assertDatabaseHas('pets', [
            'id' => $pet->id,
            'name' => 'Milo',
            'user_id' => $owner->id,
        ]);

        $this->assertDatabaseMissing('pets', [
            'id' => $pet->id,
            'user_id' => $otherUser->id,
        ]);
    }

    public function test_user_cannot_update_another_users_pet(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();

        $pet = $this->createPet($owner);

        Sanctum::actingAs($otherUser);

        $this->putJson("/api/v.0/pets/{$pet->id}", [
            'name' => 'Milo',
        ])->assertForbidden();

        $this->assertDatabaseHas('pets', [
            'id' => $pet->id,
            'name' => 'Nala',
            'user_id' => $owner->id,
        ]);
    }

    private function createPet(User $user): Pet
    {
        $species = Species::create([
            'name' => 'Dog',
            'description' => 'Test species',
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
            'about' => 'Test pet',
        ]);
    }
}
