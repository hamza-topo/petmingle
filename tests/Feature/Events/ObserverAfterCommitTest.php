<?php

namespace Tests\Feature\Events;

use App\Events\AdoptionEvent;
use App\Events\MatchEvent;
use App\Events\MessageEvent;
use App\Mail\ItsAdoption;
use App\Mail\ItsAMatch;
use App\Models\Adoption;
use App\Models\Conversation;
use App\Models\Like;
use App\Models\Message;
use App\Models\Pet;
use App\Models\Race;
use App\Models\Species;
use App\Models\User;
use Illuminate\Database\DatabaseTransactionsManager;
use Illuminate\Foundation\Testing\DatabaseTruncation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ObserverAfterCommitTest extends TestCase
{
    use DatabaseTruncation;

    protected function setUp(): void
    {
        parent::setUp();

        $transactionsManager = new DatabaseTransactionsManager;

        $this->app->instance(
            'db.transactions',
            $transactionsManager
        );

        DB::connection()->setTransactionManager(
            $transactionsManager
        );
    }

    protected function tearDown(): void
    {
        DB::connection()->unsetTransactionManager();

        parent::tearDown();
    }

    public function test_like_side_effects_run_after_commit(): void
    {

        Event::fake([
            MatchEvent::class,
        ]);

        Mail::fake();

        $firstUser = $this->createUserWithoutEvents();

        $secondUser = $this->createUserWithoutEvents();

        $firstPet = $this->createPet($firstUser);

        $secondPet = $this->createPet($secondUser);

        // Existing reciprocal like.
        // We do not want this setup row to trigger the observer being tested.
        Like::withoutEvents(function () use ($firstPet, $secondPet) {
            Like::create([
                'from' => $secondPet->id,
                'to' => $firstPet->id,
            ]);
        });

        DB::beginTransaction();

        Like::create([
            'from' => $firstPet->id,
            'to' => $secondPet->id,
        ]);

        // BEFORE COMMIT
        Event::assertNotDispatched(MatchEvent::class);
        Mail::assertNothingQueued();

        $this->assertDatabaseMissing('matches', [
            'from' => $firstPet->id,
            'to' => $secondPet->id,
        ]);

        $this->assertDatabaseMissing('matches', [
            'from' => $secondPet->id,
            'to' => $firstPet->id,
        ]);

        DB::commit();

        // AFTER COMMIT
        Event::assertDispatchedTimes(MatchEvent::class, 1);
        Mail::assertQueued(ItsAMatch::class, 2);

        $this->assertDatabaseHas('matches', [
            'from' => $firstPet->id,
            'to' => $secondPet->id,
        ]);

        $this->assertDatabaseHas('matches', [
            'from' => $secondPet->id,
            'to' => $firstPet->id,
        ]);
    }

    public function test_like_side_effects_do_not_run_after_rollback(): void
    {
        Event::fake([
            MatchEvent::class,
        ]);

        Mail::fake();

        $firstUser = $this->createUserWithoutEvents();
        $secondUser = $this->createUserWithoutEvents();

        $firstPet = $this->createPet($firstUser);
        $secondPet = $this->createPet($secondUser);

        Like::withoutEvents(function () use ($firstPet, $secondPet) {
            Like::create([
                'from' => $secondPet->id,
                'to' => $firstPet->id,
            ]);
        });

        DB::beginTransaction();

        Like::create([
            'from' => $firstPet->id,
            'to' => $secondPet->id,
        ]);

        Event::assertNotDispatched(MatchEvent::class);
        Mail::assertNothingQueued();

        $this->assertDatabaseMissing('matches', [
            'from' => $firstPet->id,
            'to' => $secondPet->id,
        ]);

        $this->assertDatabaseMissing('matches', [
            'from' => $secondPet->id,
            'to' => $firstPet->id,
        ]);

        DB::rollBack();

        Event::assertNotDispatched(MatchEvent::class);
        Mail::assertNothingQueued();

        $this->assertDatabaseMissing('matches', [
            'from' => $firstPet->id,
            'to' => $secondPet->id,
        ]);

        $this->assertDatabaseMissing('matches', [
            'from' => $secondPet->id,
            'to' => $firstPet->id,
        ]);

        $this->assertDatabaseMissing('likes', [
            'from' => $firstPet->id,
            'to' => $secondPet->id,
        ]);

        $this->assertDatabaseHas('likes', [
            'from' => $secondPet->id,
            'to' => $firstPet->id,
        ]);
    }

    public function test_message_notification_runs_after_commit(): void
    {
        Event::fake([
            MessageEvent::class,
        ]);

        $sender = $this->createUserWithoutEvents();
        $receiver = $this->createUserWithoutEvents();

        $conversation = Conversation::withoutEvents(function () use ($sender, $receiver) {
            return Conversation::create([
                'first_user_id' => $sender->id,
                'seconde_user_id' => $receiver->id,
            ]);
        });

        DB::beginTransaction();

        $message = Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $sender->id,
            'receiver_id' => $receiver->id,
            'content' => 'After commit test',
            'is_seen' => false,
        ]);

        Event::assertNotDispatched(MessageEvent::class);

        DB::commit();

        Event::assertDispatched(
            MessageEvent::class,
            function (MessageEvent $event) use ($message) {
                return $event->message->id === $message->id;
            }
        );

        Event::assertDispatchedTimes(MessageEvent::class, 1);
    }

    public function test_message_notification_does_not_run_after_rollback(): void
    {
        Event::fake([
            MessageEvent::class,
        ]);

        $sender = $this->createUserWithoutEvents();
        $receiver = $this->createUserWithoutEvents();

        $conversation = Conversation::withoutEvents(function () use ($sender, $receiver) {
            return Conversation::create([
                'first_user_id' => $sender->id,
                'seconde_user_id' => $receiver->id,
            ]);
        });

        DB::beginTransaction();

        $message = Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $sender->id,
            'receiver_id' => $receiver->id,
            'content' => 'Rollback test',
            'is_seen' => false,
        ]);

        $messageId = $message->id;

        DB::rollBack();

        Event::assertNotDispatched(MessageEvent::class);

        $this->assertDatabaseMissing('messages', [
            'id' => $messageId,
        ]);
    }

    public function test_adoption_side_effects_run_after_commit(): void
    {
        Event::fake([
            AdoptionEvent::class,
        ]);

        Mail::fake();

        $owner = $this->createUserWithoutEvents();
        $newOwner = $this->createUserWithoutEvents();

        $pet = $this->createPet($owner);

        DB::beginTransaction();

        $adoption = Adoption::create([
            'from' => $owner->id,
            'pet_id' => $pet->id,
            'to' => $newOwner->id,
        ]);

        Event::assertNotDispatched(AdoptionEvent::class);
        Mail::assertNothingQueued();

        DB::commit();

        Event::assertDispatched(
            AdoptionEvent::class,
            function (AdoptionEvent $event) use ($adoption) {
                return $event->adoption->id === $adoption->id;
            }
        );

        Event::assertDispatchedTimes(AdoptionEvent::class, 1);

        Mail::assertQueued(ItsAdoption::class, 2);
    }

    public function test_adoption_side_effects_do_not_run_after_rollback(): void
    {
        Event::fake([
            AdoptionEvent::class,
        ]);

        Mail::fake();

        $owner = $this->createUserWithoutEvents();
        $newOwner = $this->createUserWithoutEvents();

        $pet = $this->createPet($owner);

        DB::beginTransaction();

        $adoption = Adoption::create([
            'from' => $owner->id,
            'pet_id' => $pet->id,
            'to' => $newOwner->id,
        ]);

        $adoptionId = $adoption->id;

        DB::rollBack();

        Event::assertNotDispatched(AdoptionEvent::class);
        Mail::assertNothingQueued();

        $this->assertDatabaseMissing('adoptions', [
            'id' => $adoptionId,
        ]);
    }

    private function createUserWithoutEvents(): User
    {
        return User::withoutEvents(function () {
            return User::factory()->create();
        });
    }

    private function createPet(User $user): Pet
    {
        $species = Species::withoutEvents(function () {
            return Species::create([
                'name' => 'Dog',
                'description' => 'Test species',
            ]);
        });

        $race = Race::withoutEvents(function () use ($species) {
            return Race::create([
                'species_id' => $species->id,
                'name' => 'Mixed',
            ]);
        });

        return Pet::withoutEvents(function () use ($user, $species, $race) {
            return Pet::create([
                'user_id' => $user->id,
                'species_id' => $species->id,
                'race_id' => $race->id,
                'name' => 'Nala',
                'age' => 3,
                'sexe' => 1,
                'color' => 'brown',
                'images' => [],
                'about' => 'Test pet',
            ]);
        });
    }
}
