<?php

namespace App\Http\Requests\Web\User;

use Illuminate\Foundation\Http\FormRequest;

class Store extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'is_admin' => 'required|boolean',
        ];
    }

    /**
     * Get the custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'name.required' => __('The Field Name is required.'),
            'name.string' => __('The Field Name must be a valid string.'),
            'name.max' => __('The Field Name should not be more than 255 characters.'),

            'email.required' => __('The Field Email is required.'),
            'email.email' => __('Please provide a valid Email address.'),
            'email.unique' => __('This Email is already registered.'),

            'password.required' => __('The Field Password is required.'),
            'password.string' => __('The Field Password must be a valid string.'),
            'password.min' => __('The Field Password must be at least 8 characters long.'),
          
            'is_admin.required' => __('You must select an admin status.'),
            'is_admin.boolean' => __('Invalid value for Admin status.'),
        ];
    }
}
