<?php

namespace App\Repositories;

use App\Models\Component;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class ComponentRepository implements RepositoryInterface
{

    public function create(array $component): Component
    {
        return Component::create($component);
    }

    public function update(int $adoptionId, array $newModel): Component
    {
        $component = $this->getById($adoptionId);
        $component->update($newModel);
        $component->refresh();

        return $component;
    }

    /**
     * getById
     *
     * @param  mixed $adoptionId
     * @return Component
     */
    public function getById(int $adoptionId): Component
    {
        return Component::find($adoptionId);
    }

    public function delete(int $adoptionId): bool
    {
        return Component::destroy($adoptionId);
    }

    public function mismatch(array $component): bool
    {
        return Component::where(['from' => $component['from'], 'to' => $component['to']])
            ->orWhere(['from' => $component['to'], 'to' => $component['from']])->delete();
    }

    public function restore(int $adoptionId): bool
    {
        return Component::withTrashed()->findOrFail($adoptionId)->restore();
    }

    public function all(): Collection
    {
        return Component::all();
    }

    //TODO:paginate the result
    public function matches(int $petId): Collection
    {
        return Component::where('from', $petId)->with('toPet')->get();
    }

    //TODO:paginate the result
    public function mismatches(int $petId): Collection
    {
        return Component::onlyTrashed()->where('from', $petId)->get();
    }

    public function paginate()
    {
        return Component::paginate();
    }

    public function getAllFromCache(?string $key = ''): mixed
    {
        throw new \Exception('Method [getAllFromCache]. Not implemented yest!');
        return [];
    }

    public function clearCache(): bool
    {
        return true;
    }
}
