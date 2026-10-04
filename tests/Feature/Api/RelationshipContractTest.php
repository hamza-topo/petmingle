<?php

namespace Tests\Feature\Api;

use App\Models\Block;
use App\Models\Dislike;
use App\Models\Like;
use App\Models\MatchTable;
use App\Models\Pet;
use App\Models\Race;
use App\Models\Species;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class RelationshipContractTest extends TestCase
{
    use RefreshDatabase;

    public function test_likes_use_pet_ids_and_standard_pagination(): void
    {
        [$user, $pet] = $this->createUserWithPet();
        [, $targetPet] = $this->createUserWithPet('Target');

        $like = Like::withoutEvents(
            fn () => Like::create([
                'from' => $pet->id,
                'to' => $targetPet->id,
            ])
        );

        Sanctum::actingAs($user);

        $this->getJson('/api/v.0/likes')
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('message', 'List of likes.')
            ->assertJsonPath('data.0.id', $like->id)
            ->assertJsonPath(
                'data.0.from_pet_id',
                $pet->id
            )
            ->assertJsonPath(
                'data.0.to_pet_id',
                $targetPet->id
            )
            ->assertJsonPath('meta.total', 1)
            ->assertJsonPath('meta.current_page', 1);
    }

    public function test_dislikes_use_pet_ids_and_standard_pagination(): void
    {
        [$user, $pet] = $this->createUserWithPet();
        [, $targetPet] = $this->createUserWithPet('Target');

        $dislike = Dislike::withoutEvents(
            fn () => Dislike::create([
                'from' => $pet->id,
                'to' => $targetPet->id,
            ])
        );

        Sanctum::actingAs($user);

        $this->getJson('/api/v.0/dislikes')
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath(
                'message',
                'List of dislikes.'
            )
            ->assertJsonPath(
                'data.0.id',
                $dislike->id
            )
            ->assertJsonPath(
                'data.0.from_pet_id',
                $pet->id
            )
            ->assertJsonPath(
                'data.0.to_pet_id',
                $targetPet->id
            )
            ->assertJsonPath('meta.total', 1);
    }

    public function test_matches_use_pet_ids(): void
    {
        [$user, $pet] = $this->createUserWithPet();
        [, $targetPet] = $this->createUserWithPet('Target');

        $match = MatchTable::withoutEvents(
            fn () => MatchTable::create([
                'from' => $pet->id,
                'to' => $targetPet->id,
            ])
        );

        Sanctum::actingAs($user);

        $this->getJson('/api/v.0/matches')
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath(
                'message',
                'List of matches.'
            )
            ->assertJsonPath(
                'data.0.id',
                $match->id
            )
            ->assertJsonPath(
                'data.0.from_pet_id',
                $pet->id
            )
            ->assertJsonPath(
                'data.0.to_pet_id',
                $targetPet->id
            );
    }

    public function test_blocks_use_user_ids(): void
    {
        [$user] = $this->createUserWithPet();

        $target = User::withoutEvents(
            fn () => User::factory()->create()
        );

        $block = Block::withoutEvents(
            fn () => Block::create([
                'from' => $user->id,
                'to' => $target->id,
                'cause' => 1,
                'why' => 'Test block',
            ])
        );

        Sanctum::actingAs($user);

        $this->getJson('/api/v.0/blocks')
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath(
                'message',
                'List of blocks.'
            )
            ->assertJsonPath(
                'data.0.id',
                $block->id
            )
            ->assertJsonPath(
                'data.0.from_user_id',
                $user->id
            )
            ->assertJsonPath(
                'data.0.to_user_id',
                $target->id
            );
    }

    private function createUserWithPet(
        string $petName = 'Nala'
    ): array {
        $user = User::withoutEvents(
            fn () => User::factory()->create()
        );

        $species = Species::create([
            'name' => 'Dog-' . $user->id,
        ]);

        $race = Race::create([
            'species_id' => $species->id,
            'name' => 'Mixed-' . $user->id,
        ]);

        $pet = Pet::withoutEvents(
            fn () => Pet::create([
                'user_id' => $user->id,
                'species_id' => $species->id,
                'race_id' => $race->id,
                'name' => $petName,
                'age' => 3,
                'sexe' => 1,
                'color' => 'brown',
                'images' => [],
                'about' => 'Test pet',
            ])
        );

        return [$user, $pet];
    }
}