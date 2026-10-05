<?php

namespace App\Http\Requests\Api\Pet;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class Update extends FormRequest
{
    protected function prepareForValidation(): void
    {
        if ($this->has('name')) {
            $this->merge([
                'name' => trim((string) $this->input('name')),
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'species_id' => [
                'required_with:race_id',
                'integer',
                'exists:species,id',
            ],
            'race_id' => [
                'required_with:species_id',
                'integer',
                Rule::exists('races', 'id')->where(
                    'species_id',
                    $this->input('species_id')
                ),
            ],
            'name' => ['sometimes', 'string', 'min:1', 'max:25'],
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
            'race_id.exists' => __(
                'The selected race does not belong to the selected species.'
            ),
        ];
    }
}
