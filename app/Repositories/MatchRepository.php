<?php

namespace App\Repositories;

use App\Models\MatchTable;
// use App\Enums\Matched as MatchedEnum;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class MatchRepository implements RepositoryInterface
{

    public function create(array $match): MatchTable
    {
        return MatchTable::create($match);
    }

    public function update(int $matchId, array $newModel): MatchTable
    {
        $match = $this->getById($matchId);
        $match->update($newModel);
        $match->refresh();

        return $match;
    }

    /**
     * getById
     *
     * @param  mixed $matchId
     * @return MatchTable
     */
    public function getById(int $matchId): MatchTable
    {
        return MatchTable::find($matchId);
    }

    public function delete(int $matchId): bool
    {
        return MatchTable::destroy($matchId);
    }

    public function mismatch(array $match): bool
    {
        return MatchTable::where(['from' => $match['from'], 'to' => $match['to']])
            ->orWhere(['from' => $match['to'], 'to' => $match['from']])->delete();
    }

    public function restore(int $matchId): bool
    {
        return MatchTable::withTrashed()->findOrFail($matchId)->restore();
    }

    public function all(): Collection
    {
        return MatchTable::all();
    }

    //TODO:paginate the result
    public function matches(int $petId): Collection
    {
        return MatchTable::where('from', $petId)->with('toPet')->get();
    }

    //TODO:paginate the result
    public function mismatches(int $petId): Collection
    {
        return MatchTable::onlyTrashed()->where('from', $petId)->get();
    }

    public function paginate()
    {
        return MatchTable::paginate();
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
