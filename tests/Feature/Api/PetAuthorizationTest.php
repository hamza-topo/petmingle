<?php

namespace Tests\Feature\Api;

use App\Models\Pet;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class PetAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_cannot_create_pet_for_another_user(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        Sanctum::actingAs($user);

        $response = $this->postJson('/api/v.0/pets', [
            'user_id' => $otherUser->id,
            'species_id' => 1,
            'race_id' => 1,
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

        $pet = Pet::create([
            'user_id' => $user->id,
            'species_id' => 1,
            'race_id' => 1,
            'name' => 'Nala',
            'age' => 3,
            'sexe' => 1,
            'color' => 'brown',
            'images' => [],
            'about' => 'Test pet',
        ]);

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

        $pet = Pet::create([
            'user_id' => $owner->id,
            'species_id' => 1,
            'race_id' => 1,
            'name' => 'Nala',
            'age' => 3,
            'sexe' => 1,
            'color' => 'brown',
            'images' => [],
            'about' => 'Test pet',
        ]);

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

        $pet = Pet::create([
            'user_id' => $user->id,
            'species_id' => 1,
            'race_id' => 1,
            'name' => 'Nala',
            'age' => 3,
            'sexe' => 1,
            'color' => 'brown',
            'images' => [],
            'about' => 'Test pet',
        ]);

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

        $pet = Pet::create([
            'user_id' => $owner->id,
            'species_id' => 1,
            'race_id' => 1,
            'name' => 'Nala',
            'age' => 3,
            'sexe' => 1,
            'color' => 'brown',
            'images' => [],
            'about' => 'Test pet',
        ]);

        Sanctum::actingAs($otherUser);

        $this->deleteJson("/api/v.0/pets/{$pet->id}")
            ->assertForbidden();

        $this->assertDatabaseHas('pets', [
            'id' => $pet->id,
            'deleted_at' => null,
        ]);
    }
}
