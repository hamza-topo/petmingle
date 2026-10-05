<?php

namespace App\Http\Requests\Api\Location;

use Illuminate\Foundation\Http\FormRequest;

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

            // Discovery origin and requester identity are server-owned.
            'user_id' => ['prohibited'],
            'latitude' => ['prohibited'],
            'longitude' => ['prohibited'],
            'perimetre' => ['prohibited'],

            // Legacy filtering is intentionally gated until its
            // contract is normalized in the Discovery filter issue.
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
                'Discovery filters are not supported by this endpoint yet.'
            ),
        ];
    }
}
