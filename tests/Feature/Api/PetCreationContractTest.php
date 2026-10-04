<?php

namespace Tests\Feature\Api;

use App\Models\Race;
use App\Models\Species;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class PetCreationContractTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_create_pet_with_minimum_react_contract(): void
    {
        [$species, $race] = $this->taxonomy();
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $response = $this->postJson('/api/v.0/pets', [
            'species_id' => $species->id,
            'race_id' => $race->id,
            'name' => '  Milo  ',
            'age' => 4,
        ]);

        $response
            ->assertCreated()
            ->assertJsonPath('success', true)
            ->assertJsonPath('message', 'Pet has been created.')
            ->assertJsonPath('data.user_id', $user->id)
            ->assertJsonPath('data.species_id', $species->id)
            ->assertJsonPath('data.race_id', $race->id)
            ->assertJsonPath('data.name', 'Milo')
            ->assertJsonPath('data.age', 4)
            ->assertJsonPath('data.images', [])
            ->assertJsonPath('data.sexe', null)
            ->assertJsonPath('data.color', null)
            ->assertJsonPath('data.about', null);

        $this->assertDatabaseHas('pets', [
            'user_id' => $user->id,
            'species_id' => $species->id,
            'race_id' => $race->id,
            'name' => 'Milo',
            'age' => 4,
            'sexe' => null,
            'color' => null,
            'about' => null,
        ]);
    }

    public function test_creation_accepts_one_optional_image_and_returns_images_as_an_array(): void
    {
        Storage::fake('public');

        [$species, $race] = $this->taxonomy();
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $response = $this->postJson('/api/v.0/pets', [
            'species_id' => $species->id,
            'race_id' => $race->id,
            'name' => 'Milo',
            'age' => 4,
            'image' => UploadedFile::fake()->image('milo.png'),
        ]);

        $response->assertCreated();

        $images = $response->json('data.images');

        $this->assertIsArray($images);
        $this->assertCount(1, $images);
        $this->assertSame('uploads/milo.png', $images[0]);

        Storage::disk('public')->assertExists('uploads/milo.png');
    }

    public function test_validation_uses_standard_error_envelope(): void
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $this->postJson('/api/v.0/pets', [])
            ->assertUnprocessable()
            ->assertJsonPath('success', false)
            ->assertJsonPath('message', 'Validation failed.')
            ->assertJsonStructure([
                'errors' => [
                    'species_id',
                    'race_id',
                    'name',
                    'age',
                ],
            ]);
    }

    public function test_race_must_belong_to_selected_species(): void
    {
        $dog = Species::create(['name' => 'Dog']);
        $cat = Species::create(['name' => 'Cat']);

        $catRace = Race::create([
            'species_id' => $cat->id,
            'name' => 'Domestic Shorthair',
        ]);

        Sanctum::actingAs(User::factory()->create());

        $this->postJson('/api/v.0/pets', [
            'species_id' => $dog->id,
            'race_id' => $catRace->id,
            'name' => 'Milo',
            'age' => 4,
        ])
            ->assertUnprocessable()
            ->assertJsonPath('success', false)
            ->assertJsonPath('message', 'Validation failed.')
            ->assertJsonStructure([
                'errors' => ['race_id'],
            ]);
    }

    public function test_frontend_only_fields_are_rejected_instead_of_silently_persisted(): void
    {
        [$species, $race] = $this->taxonomy();

        Sanctum::actingAs(User::factory()->create());

        $this->postJson('/api/v.0/pets', [
            'species_id' => $species->id,
            'race_id' => $race->id,
            'name' => 'Milo',
            'age' => 4,
            'size' => 'Large',
            'traits' => ['Friendly'],
            'energy' => 'High energy',
            'playdate' => 'Active play',
        ])
            ->assertUnprocessable()
            ->assertJsonPath('success', false)
            ->assertJsonPath('message', 'Validation failed.')
            ->assertJsonStructure([
                'errors' => [
                    'size',
                    'traits',
                    'energy',
                    'playdate',
                ],
            ]);
    }

    public function test_legacy_images_field_is_rejected_in_favor_of_single_image_field(): void
    {
        [$species, $race] = $this->taxonomy();

        Sanctum::actingAs(User::factory()->create());

        $this->postJson('/api/v.0/pets', [
            'species_id' => $species->id,
            'race_id' => $race->id,
            'name' => 'Milo',
            'age' => 4,
            'images' => ['legacy.jpg'],
        ])
            ->assertUnprocessable()
            ->assertJsonPath('success', false)
            ->assertJsonPath('message', 'Validation failed.')
            ->assertJsonStructure([
                'errors' => ['images'],
            ]);
    }

    private function taxonomy(): array
    {
        $species = Species::create([
            'name' => 'Dog',
            'description' => 'Dogs',
        ]);

        $race = Race::create([
            'species_id' => $species->id,
            'name' => 'Labrador Retriever',
        ]);

        return [$species, $race];
    }
}
