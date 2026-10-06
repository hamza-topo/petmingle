<?php

namespace App\Repositories;

use App\Enums\App;
use App\Enums\CacheDuration;
use App\Enums\Race as EnumsRace;
use App\Models\Race;
use App\Services\CacheService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class RaceRepository
{
    public function __construct(protected CacheService $cacheService) {}

    public function create(array $race): Race
    {
        return Race::create($race);
    }

    public function update(int $raceId, array $newRace): Race
    {
        $race = $this->getById($raceId);
        $race->update($newRace);
        $race->refresh();

        return $race;
    }

    public function getById(int $raceId): Race
    {
        return Race::with('species')
            ->findOrFail($raceId);
    }

    public function delete(int $raceId): bool
    {
        return Race::destroy($raceId);
    }

    public function restore(int $raceId): bool
    {
        return Race::withTrashed()
            ->findOrFail($raceId)
            ->restore();
    }

    public function all(): Collection
    {
        return Race::all();
    }

    public function getAllFromCache(?string $key = ''): Collection
    {
        return $this->cacheService->remember(EnumsRace::CACHEKEY, CacheDuration::SHORT->value, function () {
            return Race::whereHas('species')->get();
        });
    }

    public function clearCache(): bool
    {
        return $this->cacheService->clear(EnumsRace::CACHEKEY);
    }

    /**
     * Pagination method
     *
     * @return void
     */
    public function paginate(?int $paginate = App::PAGINATE): LengthAwarePaginator
    {
        return Race::orderBy('id', App::ORDER)
            ->with('species')
            ->paginate($paginate);
    }
}
