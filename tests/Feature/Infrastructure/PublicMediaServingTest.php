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

    public function test_app_and_nginx_share_the_public_media_volume(): void
    {
        $compose = file_get_contents(
            base_path('docker-compose.yml')
        );

        $this->assertIsString($compose);
        $this->assertStringContainsString(
            'public_media:/var/www/html/storage/app/public',
            $compose
        );
        $this->assertStringContainsString(
            'public_media:/var/www/html/storage/app/public:ro',
            $compose
        );
        $this->assertStringContainsString(
            'public_media:',
            $compose
        );
    }
}
