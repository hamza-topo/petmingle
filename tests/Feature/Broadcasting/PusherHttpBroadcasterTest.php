<?php

namespace Tests\Feature\Broadcasting;

use App\Broadcasting\PusherHttpBroadcaster;
use App\Models\User;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Tests\TestCase;

class PusherHttpBroadcasterTest extends TestCase
{
    public function test_private_auth_response_is_signed_for_the_exact_private_channel(): void
    {
        $broadcaster = $this->broadcaster();

        $broadcaster->channel(
            'App.Models.User.{id}',
            fn (User $user, $id) =>
                (int) $user->id === (int) $id
        );

        $request = Request::create(
            '/broadcasting/auth',
            'POST',
            [
                'socket_id' => '123.456',
                'channel_name' =>
                    'private-App.Models.User.101',
            ]
        );

        $request->setUserResolver(
            fn () => User::factory()->make([
                'id' => 101,
            ])
        );

        $response = $broadcaster->auth($request);

        $expected = hash_hmac(
            'sha256',
            '123.456:private-App.Models.User.101',
            'test-secret'
        );

        $this->assertSame(
            'test-key:' . $expected,
            $response['auth']
        );
    }

    public function test_private_auth_rejects_another_users_channel(): void
    {
        $broadcaster = $this->broadcaster();

        $broadcaster->channel(
            'App.Models.User.{id}',
            fn (User $user, $id) =>
                (int) $user->id === (int) $id
        );

        $request = Request::create(
            '/broadcasting/auth',
            'POST',
            [
                'socket_id' => '123.456',
                'channel_name' =>
                    'private-App.Models.User.102',
            ]
        );

        $request->setUserResolver(
            fn () => User::factory()->make([
                'id' => 101,
            ])
        );

        $this->expectException(
            AccessDeniedHttpException::class
        );

        $broadcaster->auth($request);
    }

    public function test_broadcast_signs_and_posts_private_event_payload(): void
    {
        $history = [];

        $mock = new MockHandler([
            new Response(200, [], '{}'),
        ]);

        $stack = HandlerStack::create($mock);
        $stack->push(
            Middleware::history($history)
        );

        $broadcaster = new PusherHttpBroadcaster(
            new Client(['handler' => $stack]),
            'test-key',
            'test-secret',
            'test-app',
            [
                'host' => 'soketi',
                'port' => 6001,
                'scheme' => 'http',
            ]
        );

        $broadcaster->broadcast(
            ['private-App.Models.User.101'],
            'new.message',
            [
                'message' => [
                    'id' => 77,
                    'content' => 'Realtime hello',
                ],
            ]
        );

        $this->assertCount(1, $history);

        $request = $history[0]['request'];

        $this->assertSame(
            'POST',
            $request->getMethod()
        );
        $this->assertSame(
            'soketi',
            $request->getUri()->getHost()
        );
        $this->assertSame(
            6001,
            $request->getUri()->getPort()
        );
        $this->assertSame(
            '/apps/test-app/events',
            $request->getUri()->getPath()
        );

        parse_str(
            $request->getUri()->getQuery(),
            $query
        );

        $this->assertSame(
            'test-key',
            $query['auth_key']
        );
        $this->assertArrayHasKey(
            'auth_signature',
            $query
        );

        $body = json_decode(
            (string) $request->getBody(),
            true,
            flags: JSON_THROW_ON_ERROR
        );

        $this->assertSame(
            'new.message',
            $body['name']
        );
        $this->assertSame(
            ['private-App.Models.User.101'],
            $body['channels']
        );

        $payload = json_decode(
            $body['data'],
            true,
            flags: JSON_THROW_ON_ERROR
        );

        $this->assertSame(
            77,
            $payload['message']['id']
        );
        $this->assertSame(
            'Realtime hello',
            $payload['message']['content']
        );
    }

    private function broadcaster(): PusherHttpBroadcaster
    {
        return new PusherHttpBroadcaster(
            new Client([
                'handler' => HandlerStack::create(
                    new MockHandler()
                ),
            ]),
            'test-key',
            'test-secret',
            'test-app'
        );
    }
}
