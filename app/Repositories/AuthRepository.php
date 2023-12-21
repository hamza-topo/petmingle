<?php

namespace App\Repositories;

use App\Models\User;
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

    public function removeAvatar(int $userId): User
    {
        $user = User::findOrFail($userId);
        $user->update(['avatar' => '']);
        $user->refresh();
        return $user;
    }

    protected function hash(string $key): string
    {
        return Hash::make($key);
    }
}
