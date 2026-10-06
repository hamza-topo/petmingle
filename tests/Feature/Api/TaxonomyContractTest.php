<?php

namespace Tests\Feature\Api;

use App\Models\Race;
use App\Models\Species;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class TaxonomyContractTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Sanctum::actingAs(
            User::withoutEvents(
                fn () => User::factory()->create([
                    'is_admin' => false,
                ])
            )
        );
    }

    public function test_species_index_uses_standard_api_envelope(): void
    {
        $species = Species::create([
            'name' => 'Dog',
            'description' => 'Dogs',
        ]);

        $this->getJson('/api/v.0/species')
            ->assertOk()
            ->assertExactJson([
                'success' => true,
                'message' => 'List of species.',
                'data' => [
                    [
                        'id' => $species->id,
                        'name' => 'Dog',
                        'description' => 'Dogs',
                    ],
                ],
            ]);
    }

    public function test_species_show_uses_standard_api_envelope(): void
    {
        $species = Species::create([
            'name' => 'Cat',
            'description' => 'Cats',
        ]);

        $this->getJson('/api/v.0/species/'.$species->id)
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.id', $species->id)
            ->assertJsonPath('data.name', 'Cat');
    }

    public function test_race_index_uses_standard_api_envelope(): void
    {
        $species = Species::create([
            'name' => 'Dog',
        ]);

        $race = Race::create([
            'species_id' => $species->id,
            'name' => 'Golden Retriever',
        ]);

        $this->getJson('/api/v.0/races')
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.0.id', $race->id)
            ->assertJsonPath(
                'data.0.species_id',
                $species->id
            )
            ->assertJsonPath(
                'data.0.name',
                'Golden Retriever'
            );
    }

    public function test_race_show_uses_standard_api_envelope(): void
    {
        $species = Species::create([
            'name' => 'Dog',
        ]);

        $race = Race::create([
            'species_id' => $species->id,
            'name' => 'Labrador Retriever',
        ]);

        $this->getJson('/api/v.0/races/'.$race->id)
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.id', $race->id)
            ->assertJsonPath(
                'data.species_id',
                $species->id
            );
    }

    public function test_missing_taxonomy_resources_return_standard_404(): void
    {
        foreach (['species', 'races'] as $resource) {
            $this->getJson(
                '/api/v.0/'.$resource.'/999999'
            )
                ->assertNotFound()
                ->assertExactJson([
                    'success' => false,
                    'message' => 'Resource not found.',
                ]);
        }
    }
}
