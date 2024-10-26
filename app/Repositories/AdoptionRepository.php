<?php

namespace App\Repositories;

use App\Models\Adoption;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class AdoptionRepository implements RepositoryInterface
{

    public function create(array $adoption): Adoption
    {
        return Adoption::create($adoption);
    }

    public function update(int $adoptionId, array $newModel): Adoption
    {
        $adoption = $this->getById($adoptionId);
        $adoption->update($newModel);
        $adoption->refresh();

        return $adoption;
    }

    /**
     * getById
     *
     * @param  mixed $adoptionId
     * @return Adoption
     */
    public function getById(int $adoptionId): Adoption
    {
        return Adoption::find($adoptionId);
    }

    public function delete(int $adoptionId): bool
    {
        return Adoption::destroy($adoptionId);
    }

    public function mismatch(array $adoption): bool
    {
        return Adoption::where(['from' => $adoption['from'], 'to' => $adoption['to']])
            ->orWhere(['from' => $adoption['to'], 'to' => $adoption['from']])->delete();
    }

    public function restore(int $adoptionId): bool
    {
        return Adoption::withTrashed()->findOrFail($adoptionId)->restore();
    }

    public function all(): Collection
    {
        return Adoption::all();
    }

    //TODO:paginate the result
    public function matches(int $petId): Collection
    {
        return Adoption::where('from', $petId)->with('toPet')->get();
    }

    //TODO:paginate the result
    public function mismatches(int $petId): Collection
    {
        return Adoption::onlyTrashed()->where('from', $petId)->get();
    }

    public function paginate()
    {
        return Adoption::paginate();
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
