<?php

namespace App\Reducers;

use App\Enums\Provider;
use InvalidArgumentException;

class Socialite
{


    protected static ?\stdClass $user = null;


    public function __construct(array $data, string $provider)
    {
        if (\method_exists($this, $provider)) {
            \call_user_func([$this, $provider], $data);
        } else {
            throw new InvalidArgumentException("Provider '$provider' not supported.");
        }
    }

    public function user(): ?\stdClass
    {
        return self::$user;
    }

    protected function github($data): void
    {
        self::$user = new \stdClass();
        self::$user->provider_id = $data['id'] ?? '';
        self::$user->provider_name = Provider::GITHUB;
        self::$user->name = $data['name'] ?? '';
        self::$user->avatar = $data['avatar_url'] ?? '';
        self::$user->email = $data['email'] ?? '';
        //TODO::define the other fields
    }

    protected function google($data): void 
    {
        self::$user = new \stdClass();
        self::$user->provider_id = $data['id'] ?? '';
        self::$user->provider_name = Provider::GOOGLE;
        self::$user->name = $data['name'] ?? '';
        self::$user->avatar = $data['picture'] ?? '';
        self::$user->email = $data['email'] ?? '';
    }
}
