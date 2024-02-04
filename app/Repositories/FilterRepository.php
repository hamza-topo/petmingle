<?php 
namespace App\Repositories;

use App\Models\Filter;
use Illuminate\Support\Collection;

class FilterRepository implements RepositoryInterface
{
    public function create(array $filter): Filter
    {
            return Filter::create($filter);
    }

    public function update(int $filterId, array $newModel): Filter
    {
        $filter = $this->getById($filterId);
        $filter->update($newModel);
        $filter->refresh();

        return $filter;
    }

    /**
     * getById
     *
     * @param  mixed $filterId
     * @return Filter
     */
    public function getById(int $filterId): Filter
    {
        return Filter::find($filterId);
    }

    public function delete(int $filterId): bool
    {
        return Filter::destroy($filterId);
    }

    public function restore(int $filterId): bool
    {
        return Filter::withTrashed()->findOrFail($filterId)->restore();
    }

    public function all(): Collection
    {
        return Filter::all();
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