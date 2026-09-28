<?php

namespace App\Repositories;

// use App\Enums\Pet as EnumsPet;

use App\Enums\Location as EnumsLocation;
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
        return Location::where('user_id', $userId)->get();
    }

    public function paginate(): LengthAwarePaginator
    {
        return Location::paginate(EnumsLocation::PAGINATE);
    }

    public function near(array $coordinates = []) : Collection
    {
        //TODO:index fields, cache the result and add observers
        return Location::selectRaw('DISTINCT user_id')
            ->selectRaw(
                '( 6371 * acos( cos( radians(?) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(?) ) + sin( radians(?) ) * sin( radians( latitude ) ) ) ) AS distance',
                [$coordinates['latitude'], $coordinates['longitude'], $coordinates['latitude']]
            )
            ->where('user_id', '!=', $coordinates['user_id'])
            ->having('distance', '<=', $coordinates['perimeter'] ?? EnumsLocation::PERIMETRE)
            ->orderBy('distance')
            ->with('user', function ($query) {
                return $query->with('pet', function ($pet) {
                    return $pet->with('race');
                });
            })
            ->get();
    }

    public function getByIdWithTrashed(int $locationId): Location
    {
        return Location::withTrashed()->findOrFail($locationId);
    }
}
