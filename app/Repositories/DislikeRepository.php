<?php

namespace App\Repositories;

use App\Enums\Like as EnumsLike;
use App\Models\Dislike;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class DislikeRepository
{
    public function __construct(
        protected LikeRepository $likeRepository
    ) {}

    public function process(
        int $fromPetId,
        int $toPetId
    ): Dislike {
        return DB::transaction(function () use (
            $fromPetId,
            $toPetId
        ) {
            $pair = [
                'from' => $fromPetId,
                'to' => $toPetId,
            ];

            $this->likeRepository->dislike($pair);

            return Dislike::firstOrCreate($pair);
        });
    }

    public function create(array $like): Dislike
    {
        return $this->process(
            (int) $like['from'],
            (int) $like['to']
        );
    }

    public function update(
        int $likeId,
        array $newModel
    ): Dislike {
        $like = $this->getById($likeId);
        $like->update($newModel);
        $like->refresh();

        return $like;
    }

    public function getById(int $likeId): ?Dislike
    {
        return Dislike::find($likeId);
    }

    public function delete(int $likeId): bool
    {
        return Dislike::destroy($likeId);
    }

    public function restore(int $likeId): bool
    {
        return Dislike::withTrashed()
            ->findOrFail($likeId)
            ->restore();
    }

    public function all(): Collection
    {
        return Dislike::all();
    }

    public function dislikes(int $petId): LengthAwarePaginator
    {
        return Dislike::with(['to', 'from'])
            ->where('from', $petId)
            ->paginate(EnumsLike::PAGINATE);
    }

    public function paginate(): LengthAwarePaginator
    {
        return Dislike::paginate(EnumsLike::PAGINATE);
    }
}
