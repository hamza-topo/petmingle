<?php

namespace App\Broadcasting;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Broadcasting\BroadcastException;
use Illuminate\Broadcasting\Broadcasters\Broadcaster;
use Illuminate\Broadcasting\Broadcasters\UsePusherChannelConventions;
use Illuminate\Http\Request;
use JsonException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

final class PusherHttpBroadcaster extends Broadcaster
{
    use UsePusherChannelConventions;

    public function __construct(
        private ClientInterface $client,
        private string $key,
        private string $secret,
        private string $appId,
        private array $options = []
    ) {}

    public function auth(Request $request): mixed
    {
        $channelName = (string) $request->input(
            'channel_name',
            ''
        );
        $socketId = (string) $request->input(
            'socket_id',
            ''
        );

        if (
            $channelName === ''
            || $socketId === ''
            || !preg_match('/^\d+\.\d+$/', $socketId)
            || !$this->isGuardedChannel($channelName)
        ) {
            throw new AccessDeniedHttpException();
        }

        return $this->verifyUserCanAccessChannel(
            $request,
            $this->normalizeChannelName($channelName)
        );
    }

    public function validAuthenticationResponse(
        Request $request,
        mixed $result
    ): array {
        $socketId = (string) $request->input('socket_id');
        $channelName = (string) $request->input(
            'channel_name'
        );

        $signature = hash_hmac(
            'sha256',
            $socketId . ':' . $channelName,
            $this->secret
        );

        return [
            'auth' => $this->key . ':' . $signature,
        ];
    }

    public function broadcast(
        array $channels,
        string $event,
        array $payload = []
    ): void {
        $channelNames = $this->formatChannels($channels);

        if ($channelNames === []) {
            return;
        }

        try {
            $body = json_encode(
                [
                    'name' => $event,
                    'channels' => $channelNames,
                    'data' => json_encode(
                        $payload,
                        JSON_THROW_ON_ERROR
                        | JSON_UNESCAPED_SLASHES
                        | JSON_UNESCAPED_UNICODE
                    ),
                ],
                JSON_THROW_ON_ERROR
                | JSON_UNESCAPED_SLASHES
                | JSON_UNESCAPED_UNICODE
            );
        } catch (JsonException $exception) {
            throw new BroadcastException(
                'Realtime payload could not be encoded.',
                previous: $exception
            );
        }

        $path = '/apps/' . rawurlencode($this->appId)
            . '/events';

        $query = [
            'auth_key' => $this->key,
            'auth_timestamp' => (string) time(),
            'auth_version' => '1.0',
            'body_md5' => md5($body),
        ];

        ksort($query);

        $signatureBase = "POST\n"
            . $path
            . "\n"
            . http_build_query(
                $query,
                '',
                '&',
                PHP_QUERY_RFC3986
            );

        $query['auth_signature'] = hash_hmac(
            'sha256',
            $signatureBase,
            $this->secret
        );

        try {
            $response = $this->client->request(
                'POST',
                $this->apiBaseUrl() . $path,
                [
                    'query' => $query,
                    'headers' => [
                        'Content-Type' => 'application/json',
                    ],
                    'body' => $body,
                ]
            );
        } catch (GuzzleException $exception) {
            throw new BroadcastException(
                'Realtime transport is unavailable.',
                previous: $exception
            );
        }

        if (
            $response->getStatusCode() < 200
            || $response->getStatusCode() >= 300
        ) {
            throw new BroadcastException(
                'Realtime transport rejected the event.'
            );
        }
    }

    private function apiBaseUrl(): string
    {
        $scheme = (string) (
            $this->options['scheme']
            ?? (
                ($this->options['useTLS'] ?? true)
                    ? 'https'
                    : 'http'
            )
        );

        $host = (string) (
            $this->options['host']
            ?? 'api-'
                . ($this->options['cluster'] ?? 'mt1')
                . '.pusher.com'
        );

        $defaultPort = $scheme === 'https' ? 443 : 80;
        $port = (int) (
            $this->options['port']
            ?? $defaultPort
        );

        $portSuffix = $port === $defaultPort
            ? ''
            : ':' . $port;

        return $scheme
            . '://'
            . $host
            . $portSuffix;
    }
}
