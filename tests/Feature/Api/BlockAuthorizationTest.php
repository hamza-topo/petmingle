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
        ])->assertOk();

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
}