<?php

namespace App\Http\Requests\Api\Pet;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class Store extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'user_id' => 'required|integer',
            'species_id' => 'required|integer',
            'race_id' => 'required|integer',
            'name' => 'required|min:3|max:25',
            'age' => 'required',
            'images' => 'required',
            'images.*' => 'image|size:1024',
        ];
    }
    
    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'user_id.required' => \__('The Field User Id is required!'),
            'user_id.integer' => \__('The Value of user is invalid!'),
            'species_id.required' => \__('The Species Id is required!'),
            'species_id.integer' => \__('The Value of Species Id is invalid!'),
            'name.required' => \__('The Field Name is required!'),
            'name.min' => \__('The Field Name is too short!'),
            'name.max' => \__('The Field Name is too long!'),
            'images.required' => \__('The Images are required.'),
            'images.*' => \__('The Images are invalid.'),
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success'   => false,
            'message'   => 'Validation errors',
            'data'      => $validator->errors()
        ]));
    }
}
