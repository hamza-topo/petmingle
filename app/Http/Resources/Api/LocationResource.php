<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LocationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'label' => $this->label,
            'latitude' => $this->latitude !== null
                ? (float) $this->latitude
                : null,
            'longitude' => $this->longitude !== null
                ? (float) $this->longitude
                : null,
        ];
    }
}
