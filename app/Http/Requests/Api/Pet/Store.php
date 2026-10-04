<?php

namespace App\Http\Requests\Api\Pet;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class Store extends FormRequest
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
                'required',
                'integer',
                'exists:species,id',
            ],
            'race_id' => [
                'required',
                'integer',
                Rule::exists('races', 'id')->where(
                    'species_id',
                    $this->input('species_id')
                ),
            ],
            'name' => [
                'required',
                'string',
                'min:1',
                'max:25',
            ],
            'age' => [
                'required',
                'integer',
                'min:0',
                'max:30',
            ],
            'image' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png',
                'max:10240',
            ],
            'sexe' => [
                'nullable',
                'integer',
                'in:0,1',
            ],
            'color' => [
                'nullable',
                'string',
                'max:15',
            ],
            'about' => [
                'nullable',
                'string',
            ],

            // React collects these presentation/matching fields,
            // but the current Pet model has no persistence contract for them.
            'size' => ['prohibited'],
            'traits' => ['prohibited'],
            'energy' => ['prohibited'],
            'playdate' => ['prohibited'],

            // The creation endpoint accepts one optional file, not a gallery.
            'images' => ['prohibited'],
        ];
    }

    public function messages(): array
    {
        return [
            'race_id.exists' => __(
                'The selected race does not belong to the selected species.'
            ),
            'image.image' => __('The pet image must be an image.'),
            'image.mimes' => __('The pet image must be a JPG or PNG file.'),
            'image.max' => __('The pet image may not be larger than 10MB.'),
            'images.prohibited' => __(
                'Upload a single pet image using the image field.'
            ),
        ];
    }
}
