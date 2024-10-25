<?php

namespace App\Repositories;

use App\Enums\CacheDuration;
use App\Enums\Species as EnumsSpecies;
use App\Factories\TrashedFactory;
use App\Models\Species;
use App\Services\CacheService;
use Illuminate\Support\Collection;

class SpeciesRepository implements RepositoryInterface
{
    public function __construct(protected CacheService $cacheService)
    {
    }
    public function create(array $species): Species
    {
        return Species::create($species);
    }

    public function update(int $speciesId, array $newModel): Species
    {
        $species = $this->getById($speciesId);
        $species->update($newModel);
        $species->refresh();
        return $species;
    }

    /**
     * getById
     *
     * @param  mixed $speciesId
     * @return Collection
     */
    public function getById(int $speciesId): Species
    {
        return Species::findOrFail($speciesId);
    }

    public function delete(int $speciesId): bool
    {
        return Species::destroy($speciesId);
    }

    public function restore(int $speciesId): bool
    {
        return Species::withTrashed()->find($speciesId)->restore();
    }

    public function all(): Collection
    {
        return $this->getAllFromCache();
    }

    /**
     * Paginate the species resource
     *
     * @param int|null $paginate
     * @return void
     */
    public function paginate(int|null $paginate = EnumsSpecies::PAGINATE )
    {
        return TrashedFactory::apply(Species::query())->paginate($paginate);
    }

    public function getAllFromCache(?string $key = ''): mixed
    {
        return $this->cacheService->remember(EnumsSpecies::CACHEKEY, CacheDuration::SHORT->value, function () {
            return Species::all();
        });
    }

    public function clearCache(): bool
    {
        return $this->cacheService->clear(EnumsSpecies::CACHEKEY);
    }
}
