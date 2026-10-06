<?php

namespace App\Repositories;

use App\Models\Adoption;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class AdoptionRepository
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
     * @param  mixed  $adoptionId
     */
    public function getById(int $adoptionId): Adoption
    {
        return Adoption::findOrFail($adoptionId);
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

    public function paginate(): LengthAwarePaginator
    {
        return Adoption::paginate();
    }
}
