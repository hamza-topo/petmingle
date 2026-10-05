<?php

namespace App\Http\Resources\Api\Location;

use Illuminate\Http\Resources\Json\ResourceCollection;

class Near extends ResourceCollection
{
    public function toArray($request): array
    {
        return $this->collection
            ->map(function ($location): array {
                $pet = $location->user->pet;
                $race = $pet->race;

                $images = array_values(array_filter(
                    $pet->images ?? [],
                    fn ($image) =>
                        is_string($image)
                        && trim($image) !== ''
                ));

                return [
                    'owner' => [
                        'id' => (int) $location->user->id,
                        'name' => $location->user->name,
                    ],
                    'pet' => [
                        'id' => (int) $pet->id,
                        'owner_id' => (int) $pet->user_id,
                        'species_id' => (int) $pet->species_id,
                        'name' => $pet->name,
                        'age_years' => (int) $pet->age,
                        'sex' => $pet->sexe !== null
                            ? (int) $pet->sexe
                            : null,
                        'race' => [
                            'id' => (int) $race->id,
                            'species_id' => (int) $race->species_id,
                            'name' => $race->name,
                        ],
                        'images' => $images,
                        'about' => $pet->about,
                    ],
                    'distance_km' => round(
                        (float) $location->distance,
                        2
                    ),
                    'is_new' => (bool) isNew(
                        $pet->created_at
                    ),
                ];
            })
            ->values()
            ->all();
    }
}
