<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\TestResponse;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ApiValidationContractTest extends TestCase
{
    use RefreshDatabase;

    public function test_sign_in_validation_uses_standard_contract(): void
    {
        $response = $this->postJson('/api/v.0/sign-in', []);

        $this->assertValidationContract(
            $response,
            ['email', 'password']
        );
    }

    public function test_species_validation_uses_standard_contract(): void
    {
        $admin = User::withoutEvents(
            fn () => User::factory()->create([
                'is_admin' => true,
            ])
        );

        Sanctum::actingAs($admin);

        $response = $this->postJson(
            '/api/v.0/species',
            []
        );

        $this->assertValidationContract(
            $response,
            ['name']
        );
    }

    public function test_pet_validation_uses_standard_contract(): void
    {
        $user = User::withoutEvents(
            fn () => User::factory()->create()
        );

        Sanctum::actingAs($user);

        $response = $this->postJson(
            '/api/v.0/pets',
            []
        );

        $this->assertValidationContract(
            $response,
            [
                'species_id',
                'race_id',
                'name',
                'age',
                'images',
            ]
        );
    }

    public function test_location_validation_uses_standard_contract(): void
    {
        $user = User::withoutEvents(
            fn () => User::factory()->create()
        );

        Sanctum::actingAs($user);

        $response = $this->postJson(
            '/api/v.0/locations',
            []
        );

        $this->assertValidationContract(
            $response,
            ['latitude', 'longitude']
        );
    }

    public function test_like_validation_uses_standard_contract(): void
    {
        $user = User::withoutEvents(
            fn () => User::factory()->create()
        );

        Sanctum::actingAs($user);

        $response = $this->postJson(
            '/api/v.0/likes',
            []
        );

        $this->assertValidationContract(
            $response,
            ['from', 'to']
        );
    }

    public function test_message_validation_uses_standard_contract(): void
    {
        $user = User::withoutEvents(
            fn () => User::factory()->create()
        );

        Sanctum::actingAs($user);

        $response = $this->postJson(
            '/api/v.0/messages',
            []
        );

        $this->assertValidationContract(
            $response,
            ['receiver_id', 'content']
        );
    }

    private function assertValidationContract(
        TestResponse $response,
        array $fields
    ): void {
        $response
            ->assertUnprocessable()
            ->assertJsonPath('success', false)
            ->assertJsonPath(
                'message',
                'Validation failed.'
            )
            ->assertJsonStructure([
                'errors' => $fields,
            ]);

        $payload = $response->json();

        $this->assertArrayHasKey(
            'errors',
            $payload
        );

        $this->assertArrayNotHasKey(
            'data',
            $payload
        );
    }
}