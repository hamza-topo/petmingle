<?php 
namespace App\Repositories;

use App\Models\MatchTable;
// use App\Enums\Matched as MatchedEnum;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class MatchRepository implements RepositoryInterface {

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

    public function restore(int $matchId): bool
    {
        return MatchTable::withTrashed()->findOrFail($matchId)->restore();
    }

    public function all(): Collection
    {
        return MatchTable::all();
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