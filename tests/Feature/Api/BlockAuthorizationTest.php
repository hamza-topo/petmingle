<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTruncation;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class BlockAuthorizationTest extends TestCase
{
    use DatabaseTruncation;

    public function test_authenticated_user_cannot_spoof_block_sender(): void
    {
        $user = User::withoutEvents(
            fn () => User::factory()->create()
        );

        $target = User::withoutEvents(
            fn () => User::factory()->create()
        );

        $spoofedUser = User::withoutEvents(
            fn () => User::factory()->create()
        );

        Sanctum::actingAs($user);

        $this->postJson('/api/v.0/blocks', [
            'from' => $spoofedUser->id,
            'to' => $target->id,
            'cause' => 1,
            'why' => 'Test block',
        ])->assertCreated();

        $this->assertDatabaseHas('blocks', [
            'from' => $user->id,
            'to' => $target->id,
        ]);

        $this->assertDatabaseMissing('blocks', [
            'from' => $spoofedUser->id,
            'to' => $target->id,
        ]);
    }

    public function test_user_cannot_block_themselves(): void
    {
        $user = User::withoutEvents(
            fn () => User::factory()->create()
        );

        Sanctum::actingAs($user);

        $this->postJson('/api/v.0/blocks', [
            'to' => $user->id,
        ])->assertUnprocessable();

        $this->assertDatabaseMissing('blocks', [
            'from' => $user->id,
            'to' => $user->id,
        ]);
    }

    public function test_block_list_cannot_be_scoped_to_another_account_by_client_input(): void
    {
        $actor = User::withoutEvents(fn () => User::factory()->create());
        $other = User::withoutEvents(fn () => User::factory()->create());
        $target = User::withoutEvents(fn () => User::factory()->create());
        $ownBlock = \App\Models\Block::withoutEvents(fn () => \App\Models\Block::create(['from' => $actor->id, 'to' => $target->id]));
        \App\Models\Block::withoutEvents(fn () => \App\Models\Block::create(['from' => $other->id, 'to' => $target->id]));
        Sanctum::actingAs($actor);

        $this->getJson('/api/v.0/blocks?from=' . $other->id . '&user_id=' . $other->id)
            ->assertOk()->assertJsonCount(1, 'data')->assertJsonPath('data.0.id', $ownBlock->id);
    }

    public function test_anonymous_requests_cannot_list_or_create_blocks(): void
    {
        $this->getJson('/api/v.0/blocks')->assertUnauthorized();
        $this->postJson('/api/v.0/blocks', ['from' => 1, 'to' => 2])->assertUnauthorized();
        $this->assertDatabaseCount('blocks', 0);
    }
}