<?php

namespace App\Http\Resources\Api\Location;

use App\Enums\Pet;
use Illuminate\Http\Resources\Json\ResourceCollection;

class Near extends ResourceCollection
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return $this->collection->map(function ($location) {
            $pet = $location->user->pet;

            $sex = match ($pet->sexe) {
                Pet::FEMALE => __('Female'),
                Pet::MALE => __('Male'),
                default => null,
            };

            return [
                'user_name' => $location->user->name,
                'pet_name' => $pet->name,
                'pet_sexe' => $sex,
                'race' => $pet->race,
                'images' => $pet->images ?? [],
                'distance' => round($location->distance, 2) . ' km',
                'is_new' => isNew($pet->created_at),
            ];
        });
    }
}
