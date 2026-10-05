<?php

namespace Tests\Feature\Infrastructure;

use Tests\TestCase;

class PublicMediaServingTest extends TestCase
{
    public function test_nginx_serves_laravel_public_storage_directly(): void
    {
        $config = file_get_contents(
            base_path('docker/nginx/default.conf')
        );

        $this->assertIsString($config);
        $this->assertStringContainsString(
            'location ^~ /storage/',
            $config
        );
        $this->assertStringContainsString(
            'alias /var/www/html/storage/app/public/;',
            $config
        );
    }
}
