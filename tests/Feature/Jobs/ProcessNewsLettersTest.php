<?php

namespace Tests\Feature\Jobs;

use App\Enums\NewsLetter as NewsLetterType;
use App\Jobs\ProcessNewsLetters;
use App\Mail\NewsLetterMail;
use App\Models\NewsLetter;
use App\Models\Pet;
use App\Models\Race;
use App\Models\Species;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTruncation;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ProcessNewsLettersTest extends TestCase
{
    use DatabaseTruncation;
    public function test_email_newsletter_is_sent_only_to_matching_species_owners(): void
    {
        Mail::fake();

        $dogOwner = User::withoutEvents(
            fn() => User::factory()->create()
        );

        $catOwner = User::withoutEvents(
            fn() => User::factory()->create()
        );

        $dogSpecies = Species::create([
            'name' => 'Dog',
            'description' => 'Dogs',
        ]);

        $catSpecies = Species::create([
            'name' => 'Cat',
            'description' => 'Cats',
        ]);

        $dogRace = Race::create([
            'species_id' => $dogSpecies->id,
            'name' => 'Mixed Dog',
        ]);

        $catRace = Race::create([
            'species_id' => $catSpecies->id,
            'name' => 'Mixed Cat',
        ]);

        $this->createPet($dogOwner, $dogSpecies, $dogRace, 'Nala');
        $this->createPet($catOwner, $catSpecies, $catRace, 'Milo');

        $newsLetter = NewsLetter::create([
            'type' => NewsLetterType::EMAIL,
            'species_id' => $dogSpecies->id,
            'title' => 'Dog update',
            'content' => 'News for dog owners.',
            'active' => true,
        ]);

        $this->app->call([
            new ProcessNewsLetters(),
            'handle',
        ]);

        Mail::assertQueued(
            NewsLetterMail::class,
            fn(NewsLetterMail $mail) =>
            $mail->newsLetter->is($newsLetter)
                && $mail->hasTo($dogOwner->email)
        );

        Mail::assertNotQueued(
            NewsLetterMail::class,
            fn(NewsLetterMail $mail) =>
            $mail->hasTo($catOwner->email)
        );
    }

    public function test_newsletter_is_not_sent_twice_to_same_owner(): void
    {
        Mail::fake();

        $owner = User::factory()->create();

        $species = Species::create([
            'name' => 'Dog',
            'description' => 'Dogs',
        ]);

        $race = Race::create([
            'species_id' => $species->id,
            'name' => 'Mixed',
        ]);

        $this->createPet($owner, $species, $race, 'Nala');
        $this->createPet($owner, $species, $race, 'Luna');

        NewsLetter::create([
            'type' => NewsLetterType::ALL,
            'species_id' => null,
            'title' => 'PetMingle update',
            'content' => 'Platform news.',
            'active' => true,
        ]);

        $this->app->call([
            new ProcessNewsLetters(),
            'handle',
        ]);

        Mail::assertQueued(NewsLetterMail::class, 1);
    }

    private function createPet(
        User $owner,
        Species $species,
        Race $race,
        string $name
    ): Pet {
        return Pet::create([
            'user_id' => $owner->id,
            'species_id' => $species->id,
            'race_id' => $race->id,
            'name' => $name,
            'age' => 3,
            'sexe' => 1,
            'color' => 'brown',
            'images' => [],
            'about' => 'Test pet',
        ]);
    }
}
