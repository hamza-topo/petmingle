<?php

namespace App\Repositories;

use App\Models\User;
use App\Reducers\Socialite;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthRepository
{
    public function getById(int $id): User
    {
        return User::findOrFail($id);
    }

    public function getByIdWithTrashed(int $id): User
    {
        return User::withTrashed()->findOrFail($id);
    }

    public function firstOrCreateProviderUser(?array $providerUser, string $provider): User
    {
        $reducer = new Socialite($providerUser, $provider);

        return User::firstOrCreate(
            [
                'email' => $reducer->user()->email,
                'provider_id' => $reducer->user()->provider_id,
                'provider_name' => $reducer->user()->provider_name,
                'password' => bcrypt(Str::random(16)),
            ],
            (array) $reducer->user()
        );
    }

    public function delete(int $id): int
    {
        return User::destroy($id);
    }

    public function restore(int $id): bool
    {
        return User::withTrashed()->findOrFail($id)->restore();
    }

    public function removeAvatar(int $userId): User
    {
        $user = User::findOrFail($userId);
        $user->update(['avatar' => '']);
        $user->refresh();

        return $user;
    }

    public function signUp(array $user): User
    {
        if (! isset($user['password']) || ! is_string($user['password']) || $user['password'] === '') {
            throw new \InvalidArgumentException('Password is required for sign up.');
        }

        // Public registration cannot assign roles, provider identities or internal state.
        $user = Arr::only($user, ['name', 'email', 'password', 'avatar']);
        $user['is_admin'] = false;
        $user['password'] = $this->hash($user['password']);

        return User::create($user);
    }

    protected function hash(string $key): string
    {
        return Hash::make($key);
    }
}
