<?php

namespace Tests\Feature\Services;

use App\Models\Like;
use App\Models\MatchTable;
use App\Models\Pet;
use App\Models\Race;
use App\Models\Species;
use App\Models\User;
use App\Repositories\MatchRepository;
use App\Repositories\PetRepository;
use App\Services\InteractionPolicy;
use App\Services\MatchService;
use Illuminate\Foundation\Testing\DatabaseTruncation;
use RuntimeException;
use Tests\TestCase;

class MatchServiceTransactionTest extends TestCase
{
    use DatabaseTruncation;

    public function test_failed_reciprocal_match_creation_leaves_no_partial_state(): void
    {
        $species = Species::create([
            'name' => 'Dog',
            'description' => 'Test species',
        ]);

        $race = Race::create([
            'species_id' => $species->id,
            'name' => 'Mixed',
        ]);

        $firstOwner = User::factory()->create();
        $secondOwner = User::factory()->create();

        $firstPet = Pet::create([
            'user_id' => $firstOwner->id,
            'species_id' => $species->id,
            'race_id' => $race->id,
            'name' => 'Nala',
            'age' => 3,
            'sexe' => 1,
            'color' => 'brown',
            'images' => [],
            'about' => 'First test pet',
        ]);

        $secondPet = Pet::create([
            'user_id' => $secondOwner->id,
            'species_id' => $species->id,
            'race_id' => $race->id,
            'name' => 'Milo',
            'age' => 4,
            'sexe' => 1,
            'color' => 'black',
            'images' => [],
            'about' => 'Second test pet',
        ]);

        Like::withoutEvents(function () use (
            $firstPet,
            $secondPet
        ) {
            Like::create([
                'from' => $firstPet->id,
                'to' => $secondPet->id,
            ]);

            Like::create([
                'from' => $secondPet->id,
                'to' => $firstPet->id,
            ]);
        });

        $matchRepository = new class extends MatchRepository
        {
            private int $createCalls = 0;

            public function create(array $match): MatchTable
            {
                $this->createCalls++;

                if ($this->createCalls === 2) {
                    throw new RuntimeException(
                        'Simulated second match creation failure.'
                    );
                }

                return parent::create($match);
            }
        };

        $service = new MatchService(
            $matchRepository,
            new PetRepository,
            new InteractionPolicy
        );

        try {
            $service->create([
                'from' => $firstPet->id,
                'to' => $secondPet->id,
            ]);

            $this->fail('Expected reciprocal match creation to fail.');
        } catch (RuntimeException $exception) {
            $this->assertSame(
                'Simulated second match creation failure.',
                $exception->getMessage()
            );
        }

        $this->assertDatabaseCount('matches', 0);
    }

    public function test_successful_match_creation_creates_two_reciprocal_matches(): void
    {
        $species = Species::create([
            'name' => 'Dog',
            'description' => 'Test species',
        ]);

        $race = Race::create([
            'species_id' => $species->id,
            'name' => 'Mixed',
        ]);

        $firstOwner = User::factory()->create();
        $secondOwner = User::factory()->create();

        $firstPet = Pet::create([
            'user_id' => $firstOwner->id,
            'species_id' => $species->id,
            'race_id' => $race->id,
            'name' => 'Nala',
            'age' => 3,
            'sexe' => 1,
            'color' => 'brown',
            'images' => [],
            'about' => 'First test pet',
        ]);

        $secondPet = Pet::create([
            'user_id' => $secondOwner->id,
            'species_id' => $species->id,
            'race_id' => $race->id,
            'name' => 'Milo',
            'age' => 4,
            'sexe' => 1,
            'color' => 'black',
            'images' => [],
            'about' => 'Second test pet',
        ]);

        Like::withoutEvents(function () use (
            $firstPet,
            $secondPet
        ) {
            Like::create([
                'from' => $firstPet->id,
                'to' => $secondPet->id,
            ]);

            Like::create([
                'from' => $secondPet->id,
                'to' => $firstPet->id,
            ]);
        });

        $service = new MatchService(
            new MatchRepository,
            new PetRepository,
            new InteractionPolicy
        );

        $service->create([
            'from' => $firstPet->id,
            'to' => $secondPet->id,
        ]);

        $this->assertDatabaseCount('matches', 2);

        $this->assertDatabaseHas('matches', [
            'from' => $firstPet->id,
            'to' => $secondPet->id,
        ]);

        $this->assertDatabaseHas('matches', [
            'from' => $secondPet->id,
            'to' => $firstPet->id,
        ]);
    }
}
