<?php

namespace App\Http\Requests\Api\Like;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class Store extends FormRequest
{
    public function rules(): array
    {
        $sourcePetId = $this->user()?->pet?->id;

        return [
            'to_pet_id' => [
                'required',
                'integer',
                Rule::exists('pets', 'id')
                    ->whereNull('deleted_at'),
                Rule::notIn(
                    $sourcePetId !== null
                        ? [(int) $sourcePetId]
                        : []
                ),
            ],

            // Relationship source identity is server-owned.
            'from' => ['prohibited'],
            'to' => ['prohibited'],
        ];
    }

    public function messages(): array
    {
        return [
            'to_pet_id.required' => __(
                'The target pet ID is required.'
            ),
            'to_pet_id.integer' => __(
                'The target pet ID must be an integer.'
            ),
            'to_pet_id.exists' => __(
                'The selected target pet does not exist.'
            ),
            'to_pet_id.not_in' => __(
                'A pet cannot interact with itself.'
            ),
            'from.prohibited' => __(
                'The source pet is derived from authentication.'
            ),
            'to.prohibited' => __(
                'Use to_pet_id for the target pet.'
            ),
        ];
    }
}
