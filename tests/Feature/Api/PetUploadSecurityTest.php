<?php

namespace Tests\Feature\Api;

use App\Models\Pet;
use App\Models\Race;
use App\Models\Species;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class PetUploadSecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_uses_server_controlled_public_filename(): void
    {
        Storage::fake('public');

        [$species, $race] = $this->taxonomy();
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $response = $this
            ->withHeader('Accept', 'application/json')
            ->post('/api/v.0/pets', [
                'species_id' => $species->id,
                'race_id' => $race->id,
                'name' => 'Milo',
                'age' => 4,
                'image' => UploadedFile::fake()->image('client-name.png'),
            ])
            ->assertCreated();

        $path = $response->json('data.images.0');

        $this->assertIsString($path);
        $this->assertMatchesRegularExpression(
            '/^pets\/[0-9a-f-]{36}\.png$/',
            $path
        );
        $this->assertStringNotContainsString('client-name', $path);

        Storage::disk('public')->assertExists($path);

        $this->assertSame(
            'public',
            Storage::disk('public')->getVisibility($path)
        );
    }

    public function test_create_rejects_malformed_image_content(): void
    {
        Storage::fake('public');

        [$species, $race] = $this->taxonomy();

        Sanctum::actingAs(User::factory()->create());

        $this
            ->withHeader('Accept', 'application/json')
            ->post('/api/v.0/pets', [
                'species_id' => $species->id,
                'race_id' => $race->id,
                'name' => 'Milo',
                'age' => 4,
                'image' => UploadedFile::fake()->create(
                    'payload.jpg',
                    5,
                    'text/plain'
                ),
            ])
            ->assertUnprocessable()
            ->assertJsonStructure([
                'errors' => ['image'],
            ]);

        $this->assertSame(
            [],
            Storage::disk('public')->allFiles()
        );
    }

    public function test_create_rejects_unsupported_extension(): void
    {
        [$species, $race] = $this->taxonomy();

        Sanctum::actingAs(User::factory()->create());

        $this
            ->withHeader('Accept', 'application/json')
            ->post('/api/v.0/pets', [
                'species_id' => $species->id,
                'race_id' => $race->id,
                'name' => 'Milo',
                'age' => 4,
                'image' => UploadedFile::fake()->image('milo.gif'),
            ])
            ->assertUnprocessable()
            ->assertJsonStructure([
                'errors' => ['image'],
            ]);
    }

    public function test_create_rejects_image_larger_than_ten_megabytes(): void
    {
        [$species, $race] = $this->taxonomy();

        Sanctum::actingAs(User::factory()->create());

        $this
            ->withHeader('Accept', 'application/json')
            ->post('/api/v.0/pets', [
                'species_id' => $species->id,
                'race_id' => $race->id,
                'name' => 'Milo',
                'age' => 4,
                'image' => UploadedFile::fake()
                    ->image('large.jpg')
                    ->size(10241),
            ])
            ->assertUnprocessable()
            ->assertJsonStructure([
                'errors' => ['image'],
            ]);
    }

    public function test_update_replaces_image_and_deletes_previous_file(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $pet = $this->pet($user, ['pets/old.jpg']);

        Storage::disk('public')->put('pets/old.jpg', 'old');

        Sanctum::actingAs($user);

        $response = $this
            ->withHeader('Accept', 'application/json')
            ->put("/api/v.0/pets/{$pet->id}", [
                'image' => UploadedFile::fake()->image('replacement.png'),
            ])
            ->assertOk();

        $newPath = $response->json('data.images.0');

        $this->assertMatchesRegularExpression(
            '/^pets\/[0-9a-f-]{36}\.png$/',
            $newPath
        );

        Storage::disk('public')->assertExists($newPath);
        Storage::disk('public')->assertMissing('pets/old.jpg');

        $this->assertDatabaseHas('pets', [
            'id' => $pet->id,
        ]);

        $this->assertSame(
            [$newPath],
            $pet->fresh()->images
        );
    }

    public function test_invalid_replacement_keeps_existing_image(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $pet = $this->pet($user, ['pets/original.jpg']);

        Storage::disk('public')->put('pets/original.jpg', 'old');

        Sanctum::actingAs($user);

        $this
            ->withHeader('Accept', 'application/json')
            ->put("/api/v.0/pets/{$pet->id}", [
                'image' => UploadedFile::fake()->create(
                    'replacement.jpg',
                    5,
                    'text/plain'
                ),
            ])
            ->assertUnprocessable();

        Storage::disk('public')->assertExists('pets/original.jpg');

        $this->assertSame(
            ['pets/original.jpg'],
            $pet->fresh()->images
        );
    }

    public function test_owner_can_explicitly_remove_existing_image(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $pet = $this->pet($user, ['pets/original.jpg']);

        Storage::disk('public')->put('pets/original.jpg', 'old');

        Sanctum::actingAs($user);

        $this->putJson("/api/v.0/pets/{$pet->id}", [
            'remove_image' => true,
        ])
            ->assertOk()
            ->assertJsonPath('data.images', []);

        Storage::disk('public')->assertMissing('pets/original.jpg');

        $this->assertSame([], $pet->fresh()->images);
    }

    public function test_update_rejects_replacement_and_removal_together(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $pet = $this->pet($user, ['pets/original.jpg']);

        Storage::disk('public')->put('pets/original.jpg', 'old');

        Sanctum::actingAs($user);

        $this
            ->withHeader('Accept', 'application/json')
            ->put("/api/v.0/pets/{$pet->id}", [
                'remove_image' => true,
                'image' => UploadedFile::fake()->image('new.jpg'),
            ])
            ->assertUnprocessable()
            ->assertJsonStructure([
                'errors' => ['image'],
            ]);

        Storage::disk('public')->assertExists('pets/original.jpg');

        $this->assertSame(
            ['pets/original.jpg'],
            $pet->fresh()->images
        );
    }

    private function pet(User $user, array $images): Pet
    {
        [$species, $race] = $this->taxonomy();

        return Pet::create([
            'user_id' => $user->id,
            'species_id' => $species->id,
            'race_id' => $race->id,
            'name' => 'Nala',
            'age' => 3,
            'sexe' => 1,
            'color' => 'brown',
            'images' => $images,
            'about' => 'Test pet',
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
            'name' => 'Mixed',
        ]);

        return [$species, $race];
    }
}
