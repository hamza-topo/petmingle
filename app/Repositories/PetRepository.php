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

    public function create(array $pet): Pet
    {
        return Pet::create($pet);
    }

    public function update(int $petId, array $newModel): Pet
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
     * @return Pet
     */
    public function getById(int $petId): Pet
    {
        return Pet::find($petId);
    }

    public function delete(int $petId): bool
    {
        return Pet::destroy($petId);
    }

    public function restore(int $petId): bool
    {
        return Pet::withTrashed()->findOrFail($petId)->restore();
    }

    public function all(): Collection
    {
        return Pet::all();
    }

    /**
     * Method to paginate pets
     *
     * @param int $page
     * @return void
     */
    public function paginate(int|null $page = EnumsPet::PAGINATE)
    {
        return Pet::orderBy('created_at', 'DESC')->paginate($page);
    }

    public function getAllFromCache(?string $key = ''): mixed
    {
        return [];
    }

    public function clearCache(): bool
    {
        return true;
    }
}
