<?php

namespace App\Http\Requests\Api\Pet;

use Illuminate\Foundation\Http\FormRequest;

class Update extends FormRequest
{
    public function rules(): array
    {
        return [
            'species_id' => 'sometimes|integer',
            'race_id' => 'sometimes|integer',
            'name' => 'sometimes|min:3|max:25',
            'age' => 'sometimes',
            'images' => 'sometimes',
            'images.*' => 'image|size:1024',
            'sexe' => 'sometimes',
            'color' => 'sometimes',
            'about' => 'sometimes',
        ];
    }

    public function messages(): array
    {
        return [
            'species_id.integer' => __('The Value of Species Id is invalid!'),
            'name.min' => __('The Field Name is too short!'),
            'name.max' => __('The Field Name is too long!'),
            'images.*' => __('The Images are invalid.'),
        ];
    }
}