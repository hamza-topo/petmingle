<?php

namespace App\Repositories;

use App\Enums\CacheDuration;
use App\Enums\User as EnumsUser;
use App\Factories\TrashedFactory;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Services\CacheService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * User Repository
 */
class UserRepository
{
    public function __construct(protected CacheService $cacheService) {}
    public function create(array $user): User
    {
        $user['password'] = Hash::make($user['password']);

        return User::create($user);
    }

    public function update(int $id, array $userData): User
    {
        if (isset($userData['password']) && !empty($userData['password'])) {
            $userData['password'] = Hash::make($userData['password']);
        } else {
            unset($userData['password']);
        }
        $user = $this->getById($id);
        $user->update($userData);
        return $user;
    }

    public function delete(int $userId): bool
    {
        return User::destroy($userId);
    }

    public function getById(int $id): User
    {
        return User::findOrFail($id);
    }


    public function restore(int $modelId): bool
    {
        $user = User::onlyTrashed()->findOrFail($modelId);
        $user->restore();
    }

    public function all(): Collection
    {
        return User::orderBy('id', 'desc')->get();
    }

    /**
     * Method to paginate users
     *
     * @param int $page
     * @return void
     */
    public function paginate(?int $page = EnumsUser::PAGINATE): LengthAwarePaginator
    {
        return TrashedFactory::apply(User::orderBy('created_at', 'DESC'))->paginate($page);
    }

    /**
     * getAllFromCache method
     *
     * @author Topo <hamzaaitsidisaid.11@gmail.com>
     * @return mixed
     */
    public function getAllFromCache(?string $key = ''): Collection
    {
        return $this->cacheService->remember(EnumsUser::CACHEKEY, CacheDuration::SHORT->value, function () {
            return User::all();
        });
    }

    /**
     * clearCache method
     * 
     * @author Topo <hamzaaitsidisaid.11@gmail.com>
     * @return bool
     */

    public function clearCache(): bool
    {
        return $this->cacheService->clear(EnumsUser::CACHEKEY);
    }
}
