<?php

namespace Tests\Feature\Observers;

use App\Models\Block;
use App\Models\Conversation;
use App\Models\Like;
use App\Models\MatchTable;
use App\Models\Pet;
use App\Models\Race;
use App\Models\Species;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTruncation;
use Tests\TestCase;

class BlockObserverTest extends TestCase
{
    use DatabaseTruncation;

    public function test_block_archives_conversation_and_removes_match_state(): void
    {
        $firstUser = User::withoutEvents(
            fn () => User::factory()->create()
        );

        $secondUser = User::withoutEvents(
            fn () => User::factory()->create()
        );

        $species = Species::withoutEvents(fn () => Species::create([
            'name' => 'Dog',
            'description' => 'Test species',
        ]));

        $race = Race::create([
            'species_id' => $species->id,
            'name' => 'Mixed',
        ]);

        $firstPet = Pet::withoutEvents(fn () => Pet::create([
            'user_id' => $firstUser->id,
            'species_id' => $species->id,
            'race_id' => $race->id,
            'name' => 'Nala',
            'age' => 3,
            'sexe' => 1,
            'color' => 'brown',
            'images' => [],
            'about' => 'First pet',
        ]));

        $secondPet = Pet::withoutEvents(fn () => Pet::create([
            'user_id' => $secondUser->id,
            'species_id' => $species->id,
            'race_id' => $race->id,
            'name' => 'Milo',
            'age' => 4,
            'sexe' => 1,
            'color' => 'black',
            'images' => [],
            'about' => 'Second pet',
        ]));

        $conversation = Conversation::create([
            'first_user_id' => $firstUser->id,
            'seconde_user_id' => $secondUser->id,
        ]);

        Like::withoutEvents(function () use ($firstPet, $secondPet) {
            Like::create([
                'from' => $firstPet->id,
                'to' => $secondPet->id,
            ]);

            Like::create([
                'from' => $secondPet->id,
                'to' => $firstPet->id,
            ]);
        });

        MatchTable::create([
            'from' => $firstPet->id,
            'to' => $secondPet->id,
        ]);

        MatchTable::create([
            'from' => $secondPet->id,
            'to' => $firstPet->id,
        ]);

        Block::create([
            'from' => $firstUser->id,
            'to' => $secondUser->id,
        ]);

        $this->assertSoftDeleted('conversations', [
            'id' => $conversation->id,
        ]);

        $this->assertSoftDeleted('likes', [
            'from' => $firstPet->id,
            'to' => $secondPet->id,
        ]);

        $this->assertSoftDeleted('matches', [
            'from' => $firstPet->id,
            'to' => $secondPet->id,
        ]);

        $this->assertSoftDeleted('matches', [
            'from' => $secondPet->id,
            'to' => $firstPet->id,
        ]);
    }
}
