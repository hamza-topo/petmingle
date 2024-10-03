<?php

namespace App\Http\Requests\Api\Adoption;

use App\Rules\Api\Like\MatchUser;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
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
            'to' => 'required|integer|different:from',
            'pet_id' => [
                'required', 
                'integer', 
                'exists:pets,id', 
                Rule::exists('pets', 'id')->where(function ($query) {
                    $query->where('user_id', $this->input('from')); // Ensure the pet belongs to the 'from' user
                }),
            ],
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
            'to.different' => __('The To Id must be different from the From Id!'), 
            'pet_id.exists' => __('The selected pet does not exist!'),
            'pet_id.exists' => __('The selected pet does not belong to the owner with the specified From Id!'),
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
