<?php

namespace App\Repositories;

use App\Models\Component;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class ComponentRepository
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
     * @param  mixed  $adoptionId
     */
    public function getById(int $adoptionId): ?Component
    {
        return Component::find($adoptionId);
    }

    public function delete(int $adoptionId): bool
    {
        return Component::destroy($adoptionId);
    }

    public function restore(int $adoptionId): bool
    {
        return Component::withTrashed()->findOrFail($adoptionId)->restore();
    }

    public function all(): Collection
    {
        return Component::all();
    }

    public function getByName(string $name): ?Component
    {
        return Component::Where('name', $name)->first();
    }

    public function paginate(): LengthAwarePaginator
    {
        return Component::paginate();
    }
}
