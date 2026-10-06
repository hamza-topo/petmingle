<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class UnsupportedResourceActionsTest extends TestCase
{
    use RefreshDatabase;

    public function test_unsupported_interaction_actions_do_not_return_empty_success(): void
    {
        $user = User::withoutEvents(fn () => User::factory()->create());
        Sanctum::actingAs($user);

        foreach (['likes', 'dislikes'] as $resource) {
            $this->getJson('/api/v.0/'.$resource.'/1')->assertNotFound();
            $this->putJson('/api/v.0/'.$resource.'/1', [])->assertNotFound();
            $this->deleteJson('/api/v.0/'.$resource.'/1')->assertNotFound();
        }

        $this->getJson('/api/v.0/messages/1')->assertStatus(405);
    }
}
