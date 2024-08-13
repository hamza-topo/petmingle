<?php

namespace App\Repositories;

use App\Models\User;

/**
 * User Repository
 */
class UserRepository implements RepositoryInterface
{
    public function create(array $model)
    {
    }

    public function update(int $modelId, array $newModel)
    {
    }

    public function delete(int $modelId): bool
    {
        return true;
    }

    public function getById($userId)
    {
    }

    public function restore(int $modelId)
    {
    }

    public function all()
    {
        return User::orderBy('id', 'desc')->get();
    }

    /**
     * getAllFromCache method
     *
     * @author Topo <hamzaaitsidisaid.11@gmail.com>
     * @return mixed
     */
    public function getAllFromCache(): mixed
    {
        throw new \Exception("This Method is not implement for now: [getAllFromCache]", 1);
    }

    /**
     * clearCache method
     * 
     * @author Topo <hamzaaitsidisaid.11@gmail.com>
     * @return bool
     */
    public function clearCache(): bool
    {
        return true;
    }
}
