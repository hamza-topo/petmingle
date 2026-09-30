<?php

namespace App\Http\Requests\Api\Pet;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

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

    public function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Validation errors',
            'data' => $validator->errors(),
        ], 422));
    }
}