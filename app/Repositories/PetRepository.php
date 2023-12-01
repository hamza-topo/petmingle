<?php

namespace App\Repositories;

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
    public static int  $paginate = 25;

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
        return Pet::restore($petId);
    }

    public function all(): Collection
    {
       return Pet::all(); 
    }

    public function paginate()
    {
        return Pet::paginate(self::$paginate);
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
