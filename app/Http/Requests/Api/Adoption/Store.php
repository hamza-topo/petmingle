<?php

namespace App\Http\Requests\Api\Adoption;

use Illuminate\Foundation\Http\FormRequest;

class Store extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'from' => ['required', 'integer'],
            'to' => 'required|integer',
            'pet_id' => 'required|integer', // TODO::verify if the given pet belongs to to user_id
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
            'from.required' => \__('The Field From Id is required!'),
            'from.integer' => \__('The Value of From Id invalid!'),
            'to.required' => \__('The Field To Id is required!'),
            'to.integer' => \__('The Value of To Id  invalid!'),
            'pet_id.required' => \__('The Value of To Pet Id  is required!'),
            'pet_id.integer' => \__('The Value of To Pet Id  invalid!'),
        ];
    }
}
