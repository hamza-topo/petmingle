<?php

namespace App\Http\Requests\Api\Auth;

use Illuminate\Foundation\Http\FormRequest;

class SignIn extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'email' => 'required|email',
            'password' => 'required|string'
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
            'email.required' => \__('The E-mail is required.'),
            'email.email' => \__('The E-mail is invalid.'),
            'password.required' => \__('The password is required.'),
            'password.string' => \__('The password is invalid.'),
        ];
    }
}
