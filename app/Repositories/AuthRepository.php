<?php

namespace App\Repositories;

use App\Models\User;
use App\Reducers\Socialite;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthRepository
{
    public function signUp(array $user): User
    {
        $user['password'] = $this->hash($user['password']);

        return User::create($user);
    }

    public function signin(array $request): bool
    {
        if (!auth()->attempt($request))
            return false;
        return JWTAuth::fromUser(Auth::user());
    }

    public function logout(string $token)
    {
        return JWTAuth::invalidate($token);
    }

    public function getUser(string $token)
    {
        return JWTAuth::authenticate($token);
    }
    //TODO::we no longer need this 
    /**
     * Undocumented function
     *
     * @see firstOrCreateProviderUser()
     * @deprecated version
     */
    public function firstOrCreate(array $criteria, $user)
    {
        return User::firstOrCreate($criteria, $user);
    }

    public function firstOrCreateProviderUser(array $providerUser = [], string $provider): User
    {
        $reducer = new Socialite($providerUser, $provider);

        return User::firstOrCreate(
            [
                'email' =>  $reducer->user()->email,
                'provider_id' =>  $reducer->user()->provider_id,
                'provider_name' =>  $reducer->user()->provider_name,
            ],
            (array) $reducer->user()
        );
    }

    protected function hash(string $key): string
    {
        return Hash::make($key);
    }
}
