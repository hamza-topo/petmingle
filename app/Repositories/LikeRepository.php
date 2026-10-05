<?php

namespace App\Repositories;

use App\Enums\Like as EnumsLike;
use App\Models\Dislike;
use App\Models\Like;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class LikeRepository
{
    protected MatchRepository $matchRepository;

    public function __construct()
    {
        $this->matchRepository = new MatchRepository;
    }

    public function process(
        int $fromPetId,
        int $toPetId
    ): Like {
        return DB::transaction(function () use (
            $fromPetId,
            $toPetId
        ) {
            Dislike::where([
                'from' => $fromPetId,
                'to' => $toPetId,
            ])->delete();

            return Like::firstOrCreate([
                'from' => $fromPetId,
                'to' => $toPetId,
            ]);
        });
    }

    public function create(array $like): Like
    {
        return $this->process(
            (int) $like['from'],
            (int) $like['to']
        );
    }

    public function update(int $likeId, array $newModel): Like
    {
        $like = $this->getById($likeId);
        $like->update($newModel);
        $like->refresh();

        return $like;
    }

    public function getById(int $likeId): ?Like
    {
        return Like::find($likeId);
    }

    public function delete(int $likeId): bool
    {
        return Like::destroy($likeId);
    }

    public function restore(int $likeId): bool
    {
        return Like::withTrashed()
            ->findOrFail($likeId)
            ->restore();
    }

    public function all(): Collection
    {
        return Like::all();
    }

    public function likes(int $petId): LengthAwarePaginator
    {
        return Like::with(['to', 'from'])
            ->where('from', $petId)
            ->paginate(EnumsLike::PAGINATE);
    }

    public function paginate(): LengthAwarePaginator
    {
        return Like::paginate(EnumsLike::PAGINATE);
    }

    public function isLikedBefore(array $like): bool
    {
        return Like::where([
            ['from', '=', $like['from']],
            ['to', '=', $like['to']],
        ])->exists();
    }

    /**
     * Remove an active like and any persisted match pair.
     *
     * Used internally by dislike/block flows.
     */
    public function dislike(array $like): void
    {
        Like::where([
            ['from', '=', $like['from']],
            ['to', '=', $like['to']],
        ])->delete();

        $this->matchRepository->mismatch($like);
    }

    public function isMatch(array $like): bool
    {
        return Like::where([
            ['from', '=', $like['to']],
            ['to', '=', $like['from']],
        ])->exists();
    }
}
