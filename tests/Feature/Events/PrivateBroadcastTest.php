<?php

namespace Tests\Feature\Events;

use App\Events\AdoptionEvent;
use App\Events\IsWritingEvent;
use App\Events\MatchEvent;
use App\Events\MessageEvent;
use App\Models\Adoption;
use App\Models\MatchTable;
use App\Models\Message;
use App\Models\Pet;
use App\Models\User;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Support\Facades\Broadcast;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class PrivateBroadcastTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config([
            'broadcasting.default' => 'pusher',
        ]);

        Broadcast::forgetDrivers();

        require base_path('routes/channels.php');
    }

    public function test_private_events_target_only_participant_accounts_and_omit_loaded_relations(): void
    {
        $message = new Message(['sender_id' => 101, 'receiver_id' => 102, 'content' => 'Private message']);
        $message->setRelation('sender', new User(['email' => 'private@example.com']));
        $match = new MatchTable(['from' => 41, 'to' => 42]);
        $match->setRelation('fromPet', new Pet(['user_id' => 101]));
        $match->setRelation('toPet', new Pet(['user_id' => 102]));
        $reverse = new MatchTable(['from' => 42, 'to' => 41]);
        $adoption = new Adoption(['from' => 101, 'to' => 102, 'pet_id' => 41]);
        $adoption->setRelation('owner', new User(['email' => 'private@example.com']));

        foreach ([new MessageEvent($message), new MatchEvent($match, $reverse), new AdoptionEvent($adoption)] as $event) {
            $channels = $event->broadcastOn();
            $this->assertCount(2, $channels);
            foreach ($channels as $channel) {
                $this->assertInstanceOf(PrivateChannel::class, $channel);
            }
            $this->assertSame(['private-App.Models.User.101', 'private-App.Models.User.102'], array_map(fn ($c) => $c->name, $channels));
            $this->assertStringNotContainsString('private@example.com', json_encode($event->broadcastWith()));
        }
        $this->assertArrayNotHasKey('fromPet', (new MatchEvent($match, $reverse))->broadcastWith()['fromMatch']);
        $this->assertArrayNotHasKey('sender', (new MessageEvent($message))->broadcastWith()['message']);
        $this->assertSame('Private message', (new MessageEvent($message))->broadcastWith()['message']['content']);
        $typing = new IsWritingEvent(
            101,
            102,
            true
        );
        $this->assertInstanceOf(
            PrivateChannel::class,
            $typing->broadcastOn()
        );
        $this->assertSame(
            'private-App.Models.User.102',
            $typing->broadcastOn()->name
        );
        $this->assertSame([
            'sender_user_id' => 101,
            'receiver_user_id' => 102,
            'is_writing' => true,
        ], $typing->broadcastWith());
    }

    public function test_account_can_authorize_only_its_own_private_channel(): void
    {
        Sanctum::actingAs(
            User::factory()->make([
                'id' => 101,
                'is_admin' => false,
            ])
        );
        $this->postJson('/broadcasting/auth', [
            'socket_id' => '123.456',
            'channel_name' => 'private-App.Models.User.101',
        ])->assertOk();

        $this->postJson('/broadcasting/auth', [
            'socket_id' => '123.456',
            'channel_name' => 'private-App.Models.User.102',
        ])->assertForbidden();

        $this->postJson('/broadcasting/auth', [
            'socket_id' => '123.456',
            'channel_name' => 'private-auto-sitemap',
        ])->assertForbidden();
    }

    public function test_bearer_token_can_authorize_only_its_own_private_channel(): void
    {
        $user = User::factory()->create([
            'is_admin' => false,
        ]);

        $token = $user
            ->createToken('realtime-test')
            ->plainTextToken;

        $payload = [
            'socket_id' => '123.456',
            'channel_name' => 'private-App.Models.User.'
                .$user->id,
        ];

        $this
            ->withToken($token)
            ->post('/broadcasting/auth', $payload)
            ->assertOk();

        $payload['channel_name'] =
            'private-App.Models.User.'
            .($user->id + 1);

        $this
            ->withToken($token)
            ->post('/broadcasting/auth', $payload)
            ->assertForbidden();
    }

    public function test_administrator_cannot_subscribe_to_another_users_channel(): void
    {
        Sanctum::actingAs(
            User::factory()->make([
                'id' => 101,
                'is_admin' => true,
            ])
        );
        $this->postJson('/broadcasting/auth', [
            'socket_id' => '123.456',
            'channel_name' => 'private-App.Models.User.102',
        ])->assertForbidden();

        $this->postJson('/broadcasting/auth', [
            'socket_id' => '123.456',
            'channel_name' => 'private-auto-sitemap',
        ])->assertOk();
    }

    public function test_anonymous_subscriber_is_rejected(): void
    {
        $this->postJson('/broadcasting/auth', [
            'socket_id' => '123.456',
            'channel_name' => 'private-App.Models.User.101',
        ])->assertUnauthorized();
    }
}
