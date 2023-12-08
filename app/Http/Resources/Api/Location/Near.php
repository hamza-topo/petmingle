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
            return [
                'user_name' => $location->user->name,
                'pet_name' => $location->user->pet->name,
                'pet_sexe' => $location->user->pet->sexe == Pet::FEMALE ? __('Female') : __('Male'),
                'race' => $location->user->pet->race,
                'images' => $location->user->pet->images,
                'distance' => round($location->distance, 2) . ' km',
                'is_new'=> isNew($location->user->pet->created_at),
            ];
        });
    }
}
