<?php

namespace App\Providers;

use App\Broadcasting\PusherHttpBroadcaster;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\ServiceProvider;

class BroadcastServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Broadcast::extend(
            'petmingle-pusher',
            function ($app, array $config) {
                return new PusherHttpBroadcaster(
                    new Client([
                        'connect_timeout' => 2,
                        'timeout' => 5,
                    ]),
                    (string) ($config['key'] ?? ''),
                    (string) ($config['secret'] ?? ''),
                    (string) ($config['app_id'] ?? ''),
                    $config['options'] ?? []
                );
            }
        );

        Broadcast::routes([
            'middleware' => ['auth:sanctum'],
        ]);

        require base_path('routes/channels.php');
    }
}
