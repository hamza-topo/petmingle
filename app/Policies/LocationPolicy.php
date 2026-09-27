<?php

namespace App\Policies;

use App\Models\Location;
use App\Models\User;

class LocationPolicy
{
    public function view(User $user, Location $location): bool
    {
        return $location->user_id === $user->id;
    }

    public function update(User $user, Location $location): bool
    {
        return $location->user_id === $user->id;
    }

    public function delete(User $user, Location $location): bool
    {
        return $location->user_id === $user->id;
    }

    public function restore(User $user, Location $location): bool
    {
        return $location->user_id === $user->id;
    }
}