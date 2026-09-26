<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_sign_in_and_receive_sanctum_token(): void
    {
        $user = User::factory()->create();

        $response = $this->postJson('/api/v.0/sign-in', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response
            ->assertOk()
            ->assertJsonStructure([
                'token',
            ]);

        $this->assertDatabaseCount('personal_access_tokens', 1);
    }

    public function test_invalid_credentials_return_unauthorized(): void
    {
        $user = User::factory()->create();

        $response = $this->postJson('/api/v.0/sign-in', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $response->assertUnauthorized();
    }

    public function test_protected_route_rejects_unauthenticated_user(): void
    {
        $this->getJson('/api/v.0/pets')
            ->assertUnauthorized();
    }

    public function test_authenticated_user_can_access_protected_route(): void
    {
        $user = User::factory()->create();

        $token = $user->createToken('api')->plainTextToken;

        $this
            ->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/v.0/pets')
            ->assertSuccessful();
    }

    public function test_user_can_sign_out_and_current_token_is_revoked(): void
    {
        $user = User::factory()->create();

        $token = $user->createToken('api')->plainTextToken;

        $response = $this
            ->withHeader('Authorization', 'Bearer '.$token)
            ->postJson('/api/v.0/sign-out');

        $response->assertOk();

        $this->assertDatabaseCount('personal_access_tokens', 0);
    }
}