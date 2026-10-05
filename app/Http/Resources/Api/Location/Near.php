<?php

namespace App\Http\Resources\Api\Location;

use App\Models\Dislike;
use App\Models\Like;
use Illuminate\Http\Resources\Json\ResourceCollection;

class Near extends ResourceCollection
{
    public function toArray($request): array
    {
        $sourcePetId = $request->user()?->pet?->id;

        $targetPetIds = $this->collection
            ->map(
                fn ($location) =>
                    $location->user?->pet?->id
            )
            ->filter()
            ->map(fn ($petId) => (int) $petId)
            ->values();

        $likedPetIds = $sourcePetId === null
            ? collect()
            : Like::where('from', $sourcePetId)
                ->whereIn('to', $targetPetIds)
                ->pluck('to')
                ->map(fn ($petId) => (int) $petId)
                ->flip();

        $dislikedPetIds = $sourcePetId === null
            ? collect()
            : Dislike::where('from', $sourcePetId)
                ->whereIn('to', $targetPetIds)
                ->pluck('to')
                ->map(fn ($petId) => (int) $petId)
                ->flip();

        return $this->collection
            ->map(function ($location) use (
                $likedPetIds,
                $dislikedPetIds
            ): array {
                $pet = $location->user->pet;
                $race = $pet->race;

                $images = array_values(array_filter(
                    $pet->images ?? [],
                    fn ($image) =>
                        is_string($image)
                        && trim($image) !== ''
                ));

                $interaction = null;

                if ($likedPetIds->has((int) $pet->id)) {
                    $interaction = 'liked';
                } elseif (
                    $dislikedPetIds->has((int) $pet->id)
                ) {
                    $interaction = 'disliked';
                }

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
                    'interaction' => $interaction,
                ];
            })
            ->values()
            ->all();
    }
}
