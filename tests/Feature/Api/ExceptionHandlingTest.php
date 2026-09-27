<?php

namespace Tests\Feature\Api;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\ValidationException;
use RuntimeException;
use Tests\TestCase;

class ExceptionHandlingTest extends TestCase
{
    public function test_unauthenticated_api_request_returns_consistent_json_response(): void
    {
        Route::middleware('auth:sanctum')
            ->get('/api/test/authentication-error', fn() => response()->json(['ok' => true]));

        $this->getJson('/api/test/authentication-error')
            ->assertUnauthorized()
            ->assertExactJson([
                'success' => false,
                'message' => 'Unauthenticated.',
            ]);
    }

    public function test_authorization_exception_returns_consistent_json_response(): void
    {
        Route::get('/api/test/authorization-error', function () {
            throw new AuthorizationException();
        });

        $this->getJson('/api/test/authorization-error')
            ->assertForbidden()
            ->assertExactJson([
                'success' => false,
                'message' => 'Forbidden.',
            ]);
    }

    public function test_not_found_exception_returns_consistent_json_response(): void
    {
        $this->getJson('/api/test/route-does-not-exist')
            ->assertNotFound()
            ->assertExactJson([
                'success' => false,
                'message' => 'Resource not found.',
            ]);
    }

    public function test_validation_exception_preserves_validation_errors(): void
    {
        Route::post('/api/test/validation-error', function () {
            throw ValidationException::withMessages([
                'email' => ['The email field is required.'],
            ]);
        });

        $this->postJson('/api/test/validation-error')
            ->assertUnprocessable()
            ->assertExactJson([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => [
                    'email' => [
                        'The email field is required.',
                    ],
                ],
            ]);
    }

    public function test_internal_exception_does_not_expose_exception_message(): void
    {
        Route::get('/api/test/internal-error', function () {
            throw new RuntimeException('sensitive database information');
        });

        $response = $this->getJson('/api/test/internal-error');

        $response
            ->assertInternalServerError()
            ->assertExactJson([
                'success' => false,
                'message' => 'Internal server error.',
            ]);

        $response->assertDontSee('sensitive database information');
    }

    public function test_invalid_social_provider_returns_consistent_validation_response(): void
    {
        $this->getJson('/api/v.0/login/unsupported')
            ->assertUnprocessable()
            ->assertExactJson([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => [
                    'provider' => [
                        'Please login using facebook, github or google.',
                    ],
                ],
            ]);
    }
}
