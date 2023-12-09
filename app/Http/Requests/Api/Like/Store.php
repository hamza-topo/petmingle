<?php

namespace App\Http\Requests\Api\Like;

use App\Rules\Api\Like\MatchUser;
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
            'from' => ['required', 'integer', new MatchUser],
            'to' => 'required|integer',
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
