<?php

namespace Tests\Unit;

use App\Support\FrontendOrigins;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class FrontendOriginsTest extends TestCase
{
    public function test_production_origins_are_exact_https_and_deduplicated(): void
    {
        $this->assertSame(
            ['https://app.petmingle.test', 'https://admin.petmingle.test:8443'],
            FrontendOrigins::parse(' https://app.petmingle.test,https://admin.petmingle.test:8443,https://app.petmingle.test ', true)
        );
        $this->assertSame([], FrontendOrigins::parse('', true));
    }

    public function test_invalid_production_origins_are_rejected(): void
    {
        foreach (['*', 'https://*.petmingle.test', 'http://app.petmingle.test', 'https://app.petmingle.test/', 'https://app.petmingle.test/path', 'https://app.petmingle.test?token=secret', 'https://user:password@app.petmingle.test', 'https://app.petmingle.test#fragment'] as $origin) {
            try {
                FrontendOrigins::parse($origin, true);
                $this->fail('Invalid origin accepted.');
            } catch (InvalidArgumentException $exception) {
                $this->assertStringContainsString('exact HTTPS origins', $exception->getMessage());
                $this->assertStringNotContainsString('secret', $exception->getMessage());
            }
        }
    }

    public function test_local_docker_origins_remain_supported(): void
    {
        $this->assertSame(['http://localhost:5174'], FrontendOrigins::parse('http://localhost:5174', false));
    }
}
