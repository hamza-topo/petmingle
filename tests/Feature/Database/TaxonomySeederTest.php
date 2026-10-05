<?php

namespace Tests\Feature\Database;

use App\Models\Race;
use App\Models\Species;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaxonomySeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_default_seeder_bootstraps_pet_creation_taxonomy(): void
    {
        User::withoutEvents(
            fn () => $this->seed(DatabaseSeeder::class)
        );

        $dogs = Species::query()
            ->where('name', 'Dogs')
            ->firstOrFail();

        $this->assertGreaterThan(
            0,
            Race::query()
                ->where('species_id', $dogs->id)
                ->count()
        );
    }

    public function test_pet_taxonomy_seed_is_idempotent(): void
    {
        User::withoutEvents(
            fn () => $this->seed(DatabaseSeeder::class)
        );

        $speciesCount = Species::count();
        $raceCount = Race::count();

        User::withoutEvents(
            fn () => $this->seed(DatabaseSeeder::class)
        );

        $this->assertSame(
            $speciesCount,
            Species::count()
        );
        $this->assertSame(
            $raceCount,
            Race::count()
        );
    }
}
