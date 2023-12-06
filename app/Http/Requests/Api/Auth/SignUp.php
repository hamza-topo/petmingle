<?php

namespace App\Http\Requests\Api\Auth;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class SignUp extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:6|max:50'
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
            'name.required' => \__('The Name is required.'),
            'name.string' => \__('The Name invalid.'),
            'email.required' => \__('The E-mail is required.'),
            'email.email' => \__('The E-mail is invalid.'),
            'email.unique' => \__('The E-mail is invalid.'),
            'password.required' => \__('The password is required.'),
            'password.string' => \__('The password is invalid.'),
            'password.min' => \__('The password is invalid.'),
            'password.max' => \__('The password is invalid.'),
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
