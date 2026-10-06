<?php

namespace App\Http\Requests\Api\Adoption;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
            'pet_id' => ['required', 'integer', Rule::exists('pets', 'id')->where('user_id', $this->input('from'))],
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
