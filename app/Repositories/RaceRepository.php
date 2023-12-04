<?php

namespace App\Repositories;

use App\Models\Race;
use Illuminate\Database\Eloquent\Collection;

class RaceRepository
{


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
        return Race::find($raceId);
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
}
