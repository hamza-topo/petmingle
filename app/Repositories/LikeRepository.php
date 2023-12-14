<?php

namespace App\Repositories;

use App\Enums\Like as EnumsLike;
use App\Models\Like;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use PHPUnit\Framework\Constraint\IsTrue;

class LikeRepository implements RepositoryInterface
{
    protected $matchRepository;

    public function __construct()
    {
        $this->matchRepository = new MatchRepository;
    }

    public function create(array $like): Like
    {
        if (!$this->isLikedBefore($like))
            return Like::create($like);
    }

    public function update(int $likeId, array $newModel): Like
    {
        $like = $this->getById($likeId);
        $like->update($newModel);
        $like->refresh();

        return $like;
    }

    /**
     * getById
     *
     * @param  mixed $likeId
     * @return Like
     */
    public function getById(int $likeId): Like
    {
        return Like::find($likeId);
    }

    public function delete(int $likeId): bool
    {
        return Like::destroy($likeId);
    }

    public function restore(int $likeId): bool
    {
        return Like::withTrashed()->findOrFail($likeId)->restore();
    }

    public function all(): Collection
    {
        return Like::all();
    }

    public function likes(int $petId): LengthAwarePaginator
    {
        return Like::with(['to', 'from'])->where('from', $petId)->paginate(EnumsLike::PAGINATE);
    }

    public function paginate()
    {
        return Like::paginate(EnumsLike::PAGINATE);
    }

    public function isLikedBefore(array $like): bool
    {
        $isLikedBefore = Like::where([
            ['from', '=', $like['from']],
            ['to', '=', $like['to']],
        ])->get();

        return $isLikedBefore->count() > 0 ? true : false;
    }

    public function dislike(array $like): void
    {
        Like::where([
            ['from', '=', $like['from']],
            ['to', '=', $like['to']],
        ])->delete();

        $this->isMatch($like) === true ? $this->matchRepository->mismatch($like) : null;
    }

    public function isMatch(array $like): bool
    {
        $isMatch = Like::where([
            ['from', '=', $like['to']],
            ['to', '=',  $like['from']],
        ])->get();

        return $isMatch->count() > 0 ? true : false;
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
