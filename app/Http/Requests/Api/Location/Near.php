<?php

namespace App\Http\Requests\Api\Location;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class Near extends FormRequest
{
    public function rules(): array
    {
        return [
            'radius_km' => [
                'sometimes',
                'integer',
                'between:1,100',
            ],
            'page' => [
                'sometimes',
                'integer',
                'min:1',
            ],
            'per_page' => [
                'sometimes',
                'integer',
                'between:1,50',
            ],
            'species_id' => [
                'sometimes',
                'integer',
                Rule::exists('species', 'id')
                    ->whereNull('deleted_at'),
            ],
            'race_id' => [
                'sometimes',
                'integer',
                Rule::exists('races', 'id')->where(
                    function ($query) {
                        $query->whereNull('deleted_at');

                        if ($this->filled('species_id')) {
                            $query->where(
                                'species_id',
                                (int) $this->input('species_id')
                            );
                        }
                    }
                ),
            ],

            'user_id' => ['prohibited'],
            'latitude' => ['prohibited'],
            'longitude' => ['prohibited'],
            'perimetre' => ['prohibited'],
            'filters' => ['prohibited'],
        ];
    }

    public function messages(): array
    {
        return [
            'radius_km.integer' => __(
                'The discovery radius must be a whole number of kilometers.'
            ),
            'radius_km.between' => __(
                'The discovery radius must be between 1 and 100 kilometers.'
            ),
            'page.integer' => __(
                'The discovery page must be a whole number.'
            ),
            'page.min' => __(
                'The discovery page must be at least 1.'
            ),
            'per_page.integer' => __(
                'The discovery page size must be a whole number.'
            ),
            'per_page.between' => __(
                'The discovery page size must be between 1 and 50.'
            ),
            'species_id.exists' => __(
                'The selected species does not exist.'
            ),
            'race_id.exists' => __(
                'The selected race does not belong to the selected species.'
            ),
            'user_id.prohibited' => __(
                'Requester identity is derived from authentication.'
            ),
            'latitude.prohibited' => __(
                'Discovery latitude is derived from your saved location.'
            ),
            'longitude.prohibited' => __(
                'Discovery longitude is derived from your saved location.'
            ),
            'perimetre.prohibited' => __(
                'Use radius_km for the discovery radius.'
            ),
            'filters.prohibited' => __(
                'Use the supported top-level Discovery filter fields.'
            ),
        ];
    }
}
