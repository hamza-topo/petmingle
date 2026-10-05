<?php

namespace App\Http\Requests\Api\Pet;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class Update extends FormRequest
{
    public function rules(): array
    {
        return [
            'species_id' => ['sometimes', 'integer'],
            'race_id' => ['sometimes', 'integer'],
            'name' => ['sometimes', 'string', 'min:3', 'max:25'],
            'age' => ['sometimes', 'integer', 'min:0', 'max:30'],
            'image' => [
                Rule::prohibitedIf(
                    fn () => $this->boolean('remove_image')
                ),
                ...PetImageRules::optional(),
            ],
            'remove_image' => ['sometimes', 'boolean'],
            'images' => ['prohibited'],
            'sexe' => ['sometimes', 'nullable', 'integer', 'in:0,1'],
            'color' => ['sometimes', 'nullable', 'string', 'max:15'],
            'about' => ['sometimes', 'nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            ...PetImageRules::messages(),
            'image.prohibited' => __(
                'Choose either a replacement image or image removal, not both.'
            ),
            'images.prohibited' => __(
                'Use image for replacement or remove_image for deletion.'
            ),
        ];
    }
}
