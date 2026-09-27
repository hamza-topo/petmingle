<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function removeAvatar(User $actor, User $target): bool
    {
        return $actor->id === $target->id;
    }

    public function disable(User $actor, User $target): bool
    {
        return $actor->id === $target->id;
    }

    public function enable(User $actor, User $target): bool
    {
        return (bool)$actor->is_admin;
    }
}
