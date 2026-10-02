<?php

namespace Tests\Feature\Api;

use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class CorsTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['cors.allowed_origins' => [
            'http://localhost:5173', 'http://127.0.0.1:5173',
            'http://localhost:5174', 'http://127.0.0.1:5174',
        ]]);
    }

    public static function localOrigins(): array
    {
        return array_map(fn ($origin) => [$origin], [
            'http://localhost:5173', 'http://127.0.0.1:5173',
            'http://localhost:5174', 'http://127.0.0.1:5174',
        ]);
    }

    #[DataProvider('localOrigins')]
    public function test_bearer_preflight_accepts_only_configured_frontend_origins(string $origin): void
    {
        $response = $this->options('/api/v.0/sign-in', [], [
            'Origin' => $origin,
            'Access-Control-Request-Method' => 'POST',
            'Access-Control-Request-Headers' => 'authorization,content-type,accept',
        ])->assertNoContent()->assertHeader('Access-Control-Allow-Origin', $origin)
            ->assertHeaderMissing('Access-Control-Allow-Credentials');

        $this->assertStringContainsString('authorization', strtolower($response->headers->get('Access-Control-Allow-Headers')));
        $this->assertStringContainsString('POST', $response->headers->get('Access-Control-Allow-Methods'));
    }

    public function test_unauthenticated_identity_errors_are_readable_by_the_allowed_frontend(): void
    {
        $this->getJson('/api/v.0/me', ['Origin' => 'http://localhost:5174'])
            ->assertUnauthorized()->assertHeader('Access-Control-Allow-Origin', 'http://localhost:5174')
            ->assertHeaderMissing('Access-Control-Allow-Credentials');
    }

    public function test_unconfigured_origins_do_not_receive_cors_permission(): void
    {
        foreach (['https://untrusted.example', 'http://localhost:51740'] as $origin) {
            $this->options('/api/v.0/me', [], [
                'Origin' => $origin, 'Access-Control-Request-Method' => 'GET',
                'Access-Control-Request-Headers' => 'authorization',
            ])->assertHeaderMissing('Access-Control-Allow-Origin');
        }
    }

    public function test_explicit_production_origin_replaces_the_local_allowlist(): void
    {
        config(['cors.allowed_origins' => ['https://app.petmingle.example']]);
        $this->getJson('/api/v.0/me', ['Origin' => 'https://app.petmingle.example'])
            ->assertUnauthorized()->assertHeader('Access-Control-Allow-Origin', 'https://app.petmingle.example');
        $this->getJson('/api/v.0/me', ['Origin' => 'http://localhost:5174'])
            // The CORS library emits a constant header for a single allowed origin.
            // It must not echo localhost: browsers reject the origin mismatch.
            ->assertUnauthorized()->assertHeader('Access-Control-Allow-Origin', 'https://app.petmingle.example');
    }
}
