<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PetResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'species_id' => $this->species_id,
            'race_id' => $this->race_id,
            'name' => $this->name,
            'age' => $this->age,
            'sexe' => $this->sexe,
            'color' => $this->color,
            'images' => $this->images ?? [],
            'about' => $this->about,
        ];
    }
}