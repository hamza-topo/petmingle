<?php

namespace Tests\Feature\Admin;

use App\Http\Middleware\IsAdmin;
use App\Models\Adoption;
use App\Models\Pet;
use App\Models\Race;
use App\Models\Species;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdoptionAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_access_adoption_index(): void
    {
        $admin = User::factory()->create([
            'is_admin' => true,
        ]);

        // Disable middleware so this test specifically exercises the policy.
        $this->withoutMiddleware(IsAdmin::class);

        $this->actingAs($admin);

        $this->get('/admin/adoptions')
            ->assertOk();
    }

    public function test_non_admin_cannot_access_adoption_index(): void
    {
        $user = User::factory()->create([
            'is_admin' => false,
        ]);

        // Isolate AdoptionPolicy from the existing admin middleware.
        $this->withoutMiddleware(IsAdmin::class);

        $this->actingAs($user);

        $this->get('/admin/adoptions')
            ->assertForbidden();
    }

    public function test_non_admin_cannot_access_adoption_create_page(): void
    {
        $user = User::factory()->create([
            'is_admin' => false,
        ]);

        $this->withoutMiddleware(IsAdmin::class);

        $this->actingAs($user);

        $this->get('/admin/adoptions/create')
            ->assertForbidden();
    }

    public function test_admin_can_view_adoption(): void
    {
        $admin = User::factory()->create([
            'is_admin' => true,
        ]);

        $adoption = $this->createAdoption();

        $this->withoutMiddleware(IsAdmin::class);

        $this->actingAs($admin);

        $this->get("/admin/adoptions/{$adoption->id}")
            ->assertOk();
    }

    public function test_non_admin_cannot_view_adoption(): void
    {
        $user = User::factory()->create([
            'is_admin' => false,
        ]);

        $adoption = $this->createAdoption();

        $this->withoutMiddleware(IsAdmin::class);

        $this->actingAs($user);

        $this->get("/admin/adoptions/{$adoption->id}")
            ->assertForbidden();
    }

    public function test_non_admin_cannot_update_adoption(): void
    {
        $user = User::factory()->create([
            'is_admin' => false,
        ]);

        $adoption = $this->createAdoption();

        $this->withoutMiddleware(IsAdmin::class);

        $this->actingAs($user);

        $this->put("/admin/adoptions/{$adoption->id}", [
            'from' => $adoption->from,
            'pet_id' => $adoption->pet_id,
            'to' => $adoption->to,
        ])->assertForbidden();
    }

    private function createAdoption(): Adoption
    {
        $owner = User::factory()->create();
        $newOwner = User::factory()->create();

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

        return Adoption::withoutEvents(function () use ($owner, $newOwner, $pet) {
            return Adoption::create([
                'from' => $owner->id,
                'pet_id' => $pet->id,
                'to' => $newOwner->id,
            ]);
        });
    }
}
