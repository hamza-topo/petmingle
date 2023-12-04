<?php

namespace App\Repositories;

use App\Enums\Pet as EnumsPet;
use App\Models\Pet;
use Illuminate\Database\Eloquent\Collection;

/**
 * Pet Repository Class
 *
 * @author Topo <hamzaaitsidisaid.11@gmail.com>
 * @return mixed
 */
class PetRepository implements RepositoryInterface
{
    //TODO::make this as enum

    public function create(array $pet): Collection
    {
        return Pet::create($pet);
    }

    public function update(int $petId, array $newModel): Collection
    {
        $pet = $this->getById($petId);
        $pet->update($newModel);
        $pet->refresh();

        return $pet;
    }

    /**
     * getById
     *
     * @param  mixed $petId
     * @return Collection
     */
    public function getById(int $petId): Collection
    {
        return Pet::find($petId);
    }

    public function delete(int $petId): bool
    {
        return Pet::destroy($petId);
    }

    public function restore(int $petId): bool
    {
        return Pet::withTrashed()->find($petId)->restore();
    }

    public function all(): Collection
    {
        return Pet::all();
    }

    public function paginate()
    {
        return Pet::paginate(EnumsPet::PAGINATE);
    }

    public function getAllFromCache(): mixed
    {
        return [];
    }

    public function clearCache(): bool
    {
        return true;
    }
}
