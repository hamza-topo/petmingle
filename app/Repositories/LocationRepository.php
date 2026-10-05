<?php

namespace App\Repositories;

// use App\Enums\Pet as EnumsPet;

use App\Enums\Location as EnumsLocation;
use App\Models\Block;
use App\Models\Location;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Pet Repository Class
 *
 * @author Topo <hamzaaitsidisaid.11@gmail.com>
 * @return mixed
 */
class LocationRepository
{
    //TODO::make this as enum

    public function create(array $location): Location
    {
        return Location::create($location);
    }

    public function update(int $locationId, array $newModel): Location
    {
        $location = $this->getById($locationId);
        $location->update($newModel);
        $location->refresh();

        return $location;
    }

    /**
     * getById
     *
     * @param  string $locationId
     * @return Location
     */
    public function getById(string $locationId): Location
    {
        return Location::findOrFail($locationId);
    }

    public function delete(int $locationId): bool
    {
        return Location::destroy($locationId);
    }

    public function restore(int $locationId): bool
    {
        return $this->getByIdWithTrashed($locationId)->restore();
    }

    public function all(): Collection
    {
        return Location::all();
    }

    public function forUser(int $userId): Collection
    {
        return Location::where('user_id', $userId)
            ->orderByDesc('id')
            ->get();
    }

    public function paginate(): LengthAwarePaginator
    {
        return Location::paginate(EnumsLocation::PAGINATE);
    }

    public function currentForUser(int $userId): ?Location
    {
        return Location::where('user_id', $userId)
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->orderByDesc('id')
            ->first();
    }

    public function nearbyForUser(
        int $requesterUserId,
        float $latitude,
        float $longitude,
        int $radiusKm = EnumsLocation::PERIMETRE,
        int $perPage = 24,
        int $page = 1
    ): LengthAwarePaginator {
        $blockedUserIds = Block::query()
            ->where(function ($query) use ($requesterUserId) {
                $query->where('from', $requesterUserId)
                    ->orWhere('to', $requesterUserId);
            })
            ->get(['from', 'to'])
            ->flatMap(
                fn (Block $block) => [
                    (int) $block->from,
                    (int) $block->to,
                ]
            )
            ->reject(
                fn (int $userId) =>
                    $userId === $requesterUserId
            )
            ->unique()
            ->values()
            ->all();

        $latestUsableLocationIds = Location::query()
            ->selectRaw('MAX(id)')
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->groupBy('user_id');

        $query = Location::query()
            ->select('locations.*')
            ->selectRaw(
                '(6371 * acos(LEAST(1, GREATEST(-1, cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude)))))) AS distance',
                [$latitude, $longitude, $latitude]
            )
            ->whereIn('locations.id', $latestUsableLocationIds)
            ->where('locations.user_id', '!=', $requesterUserId)
            ->when(
                $blockedUserIds !== [],
                fn ($query) =>
                    $query->whereNotIn(
                        'locations.user_id',
                        $blockedUserIds
                    )
            )
            ->whereHas('user.pet.race')
            ->havingRaw('distance <= ?', [$radiusKm])
            ->orderBy('distance')
            ->orderBy('locations.id')
            ->with([
                'user.pet.race',
            ]);

        return $query->paginate(
            $perPage,
            ['*'],
            'page',
            $page
        );

    }

    public function getByIdWithTrashed(int $locationId): Location
    {
        return Location::withTrashed()->findOrFail($locationId);
    }
}
