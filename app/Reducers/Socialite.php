<?php

namespace App\Reducers;

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
        self::$user->name = $data['name'] ?? '';
        self::$user->avatar = $data['avatar_url'] ?? '';
        self::$user->email = $data['email'] ?? '';
        //TODO::define the other fields
    }
}
