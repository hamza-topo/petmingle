<?php

namespace App\Repositories;

use App\Enums\App;
use App\Enums\CacheDuration;
use App\Enums\Race as EnumsRace;
use App\Models\Race;
use App\Services\CacheService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class RaceRepository implements RepositoryInterface
{

    public function __construct(protected CacheService $cacheService)
    {
        
    }

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

    public function getById(int $raceId) 
    {
        return Race::with('species')->where('id', $raceId)->first();
    }

    public function delete(int $raceId): bool
    {
        return Race::destroy($raceId);
    }

    public function restore(int $raceId): bool
    {
        return Race::withTrashed()->find($raceId)->restore();
    }

    public function all()
    {
        return Race::all();
    }

    public function getAllFromCache(): mixed
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
     * @param int|null $paginate
     * @return void
     */
    public function paginate(int|null $paginate = App::PAGINATE)
    {   
        return Race::OrderBy('id', App::ORDER)->with('species')->paginate($paginate);
    }
}
