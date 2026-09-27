<?php

namespace App\Policies;

use App\Models\Adoption;
use App\Models\User;

class AdoptionPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->is_admin === true;
    }

    public function view(User $user, Adoption $adoption): bool
    {
        return $user->is_admin === true;
    }

    public function create(User $user): bool
    {
        return $user->is_admin === true;
    }

    public function update(User $user, Adoption $adoption): bool
    {
        return $user->is_admin === true;
    }

    public function delete(User $user, Adoption $adoption): bool
    {
        return $user->is_admin === true;
    }

    public function restore(User $user, Adoption $adoption): bool
    {
        return $user->is_admin === true;
    }
}