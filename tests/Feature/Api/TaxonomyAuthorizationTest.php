<?php

namespace Tests\Feature\Api;

use App\Models\Race;
use App\Models\Species;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class TaxonomyAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public static function mutations(): iterable
    {
        foreach (['species', 'races'] as $resource) {
            foreach (['POST', 'PUT', 'PATCH', 'DELETE', 'restore'] as $action) {
                foreach ([false, true] as $admin) {
                    yield "$resource $action ".($admin ? 'admin' : 'member') => [$resource, $action, $admin];
                }
            }
        }
    }

    #[DataProvider('mutations')]
    public function test_only_administrators_can_mutate_taxonomy(string $resource, string $action, bool $admin): void
    {
        $user = User::withoutEvents(fn () => User::factory()->create(['is_admin' => $admin]));
        $species = Species::create(['name' => 'Original species']);
        $model = $resource === 'species' ? $species : Race::create(['species_id' => $species->id, 'name' => 'Original race']);
        if ($action === 'restore') {
            $model->delete();
        }
        Sanctum::actingAs($user);
        $url = '/api/v.0/'.$resource;
        if ($action === 'restore') {
            $url .= '/restore/'.$model->id;
        } elseif ($action !== 'POST') {
            $url .= '/'.$model->id;
        }
        $payload = ['name' => 'Changed taxonomy', 'species_id' => $species->id];
        $response = $this->json($action === 'restore' ? 'PUT' : $action, $url, $payload);

        if (! $admin) {
            $response->assertForbidden();
            $this->assertDatabaseMissing($resource, ['name' => 'Changed taxonomy']);
            $this->assertSame($action === 'restore', $model->fresh()->trashed());

            return;
        }

        $response->assertSuccessful();
        if ($action === 'DELETE') {
            $this->assertSoftDeleted($model);
        } elseif ($action === 'restore') {
            $this->assertNotSoftDeleted($model);
        } else {
            $this->assertDatabaseHas($resource, ['name' => 'Changed taxonomy']);
        }
    }

    public function test_member_can_still_read_taxonomy(): void
    {
        $user = User::withoutEvents(fn () => User::factory()->create(['is_admin' => false]));
        $species = Species::create(['name' => 'Dog']);
        $race = Race::create(['species_id' => $species->id, 'name' => 'Golden Retriever']);
        Sanctum::actingAs($user);

        foreach (['species', 'species/'.$species->id, 'races', 'races/'.$race->id] as $path) {
            $this->getJson('/api/v.0/'.$path)->assertOk();
        }
    }
}
