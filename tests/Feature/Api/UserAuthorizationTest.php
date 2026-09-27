<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class UserAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_remove_own_avatar(): void
    {
        $user = User::factory()->create([
            'avatar' => 'uploads/avatar.jpg',
        ]);

        Sanctum::actingAs($user);

        $this->putJson("/api/v.0/remove-avatar/{$user->id}")
            ->assertOk();

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'avatar' => '',
        ]);
    }

    public function test_user_cannot_remove_another_users_avatar(): void
    {
        $user = User::factory()->create();

        $otherUser = User::factory()->create([
            'avatar' => 'uploads/avatar.jpg',
        ]);

        Sanctum::actingAs($user);

        $this->putJson("/api/v.0/remove-avatar/{$otherUser->id}")
            ->assertForbidden();

        $this->assertDatabaseHas('users', [
            'id' => $otherUser->id,
            'avatar' => 'uploads/avatar.jpg',
        ]);
    }

    public function test_user_can_disable_own_account(): void
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $this->deleteJson("/api/v.0/disable-account/{$user->id}")
            ->assertOk();

        $this->assertSoftDeleted('users', [
            'id' => $user->id,
        ]);
    }

    public function test_user_cannot_disable_another_users_account(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        Sanctum::actingAs($user);

        $this->deleteJson("/api/v.0/disable-account/{$otherUser->id}")
            ->assertForbidden();

        $this->assertDatabaseHas('users', [
            'id' => $otherUser->id,
            'deleted_at' => null,
        ]);
    }

    public function test_admin_can_enable_disabled_account(): void
    {
        $admin = User::factory()->create([
            'is_admin' => true,
        ]);

        $user = User::factory()->create();
        $user->delete();

        Sanctum::actingAs($admin);

        $this->putJson("/api/v.0/enable-account/{$user->id}")
            ->assertOk();

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'deleted_at' => null,
        ]);
    }

    public function test_non_admin_cannot_enable_disabled_account(): void
    {
        $user = User::factory()->create();
        $disabledUser = User::factory()->create();

        $disabledUser->delete();

        Sanctum::actingAs($user);

        $this->putJson("/api/v.0/enable-account/{$disabledUser->id}")
            ->assertForbidden();

        $this->assertSoftDeleted('users', [
            'id' => $disabledUser->id,
        ]);
    }
}
