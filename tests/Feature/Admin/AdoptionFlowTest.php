<?php

namespace Tests\Feature\Admin;

use App\Events\AdoptionEvent;
use App\Mail\ItsAdoption;
use App\Models\Pet;
use App\Models\Race;
use App\Models\Species;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTruncation;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class AdoptionFlowTest extends TestCase
{
    use DatabaseTruncation;

    protected function tearDown(): void
    {
        // DatabaseTruncation cleans before committed-flow tests.
        // Clean again afterwards so transaction-based tests that run
        // later in the same process never observe our committed rows.
        $this->truncateTablesForAllConnections();

        parent::tearDown();
    }

    public function test_admin_can_create_adoption_and_dispatch_persisted_notifications(): void
    {
        Event::fake([
            AdoptionEvent::class,
        ]);

        Mail::fake();

        $admin = User::withoutEvents(
            fn () => User::factory()->create([
                'is_admin' => true,
            ])
        );

        $owner = User::withoutEvents(
            fn () => User::factory()->create()
        );

        $newOwner = User::withoutEvents(
            fn () => User::factory()->create()
        );

        $pet = $this->createPet($owner);

        $this->actingAs($admin);

        $this->post('/admin/adoptions', [
            'from' => $newOwner->id,
            'pet_id' => $pet->id,
            'to' => $owner->id,
        ])->assertSessionHasErrors('pet_id');
        $this->assertDatabaseCount('adoptions', 0);

        $this->post('/admin/adoptions', [
            'from' => $owner->id,
            'pet_id' => $pet->id,
            'to' => $newOwner->id,
        ])
            ->assertRedirect(
                route('admin.adoptions.index')
            );

        $this->assertDatabaseHas('adoptions', [
            'from' => $owner->id,
            'pet_id' => $pet->id,
            'to' => $newOwner->id,
            'deleted_at' => null,
        ]);

        Event::assertDispatchedTimes(
            AdoptionEvent::class,
            1
        );

        Mail::assertQueued(
            ItsAdoption::class,
            2
        );
    }

    public function test_admin_middleware_rejects_anonymous_and_non_admin_requests(): void
    {
        $this->get('/admin/adoptions')
            ->assertUnauthorized();

        $user = User::withoutEvents(
            fn () => User::factory()->create([
                'is_admin' => false,
            ])
        );

        $this->actingAs($user);

        $this->get('/admin/adoptions')
            ->assertForbidden();
    }

    private function createPet(
        User $owner
    ): Pet {
        $species = Species::withoutEvents(
            fn () => Species::create([
                'name' => 'Dog-'.$owner->id,
                'description' => 'Critical adoption species',
            ])
        );

        $race = Race::withoutEvents(
            fn () => Race::create([
                'species_id' => $species->id,
                'name' => 'Mixed-'.$owner->id,
            ])
        );

        return Pet::withoutEvents(
            fn () => Pet::create([
                'user_id' => $owner->id,
                'species_id' => $species->id,
                'race_id' => $race->id,
                'name' => 'Nala',
                'age' => 3,
                'sexe' => 1,
                'color' => 'brown',
                'images' => [],
                'about' => 'Adoption flow pet',
            ])
        );
    }
}
