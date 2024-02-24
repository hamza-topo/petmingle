<?php

namespace App\Repositories;

use App\Enums\Like as EnumsLike;
use App\Models\Dislike;
use App\Models\Like;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

use function PHPUnit\Framework\isTrue;

class DislikeRepository implements RepositoryInterface
{
    public function __construct(protected LikeRepository $likeRepository)
    {
    }

    public function create(array $like): Dislike
    {
        $this->isLikedBefore($like);
        return Dislike::create($like);
    }

    public function update(int $likeId, array $newModel): DisLike
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
     * @return Dislike
     */
    public function getById(int $likeId): DisLike
    {
        return Dislike::find($likeId);
    }

    public function delete(int $likeId): bool
    {
        return Dislike::destroy($likeId);
    }

    public function restore(int $likeId): bool
    {
        return Dislike::withTrashed()->findOrFail($likeId)->restore();
    }

    public function all(): Collection
    {
        return Dislike::all();
    }

    public function dislikes(int $petId): LengthAwarePaginator
    {
        return Dislike::with(['to', 'from'])->where('from', $petId)->paginate(EnumsLike::PAGINATE);
    }

    public function paginate()
    {
        return Dislike::paginate(EnumsLike::PAGINATE);
    }

    public function isLikedBefore(array $like): void
    {
        $isLikedBefore = $this->likeRepository->isLikedBefore($like);
        $isLikedBefore === true ? $this->likeRepository->dislike($like) : null;
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
