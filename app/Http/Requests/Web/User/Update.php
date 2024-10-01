<?php

namespace App\Http\Requests\Web\User;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class Update extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $this->route('user'),
            'password' => 'nullable|string|min:8|confirmed',
            'is_admin' => 'required|boolean',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'name.required' => __('The Field User Name is required.'),
            'email.required' => __('The Field User Email is required.'),
            'email.unique' => __('This email is already taken.'),
            'password.min' => __('The password must be at least 8 characters long.'),
            'password.confirmed' => __('The password confirmation does not match.'),
            'is_admin.required' => __('The Admin Status is required.'),
            'is_admin.boolean' => __('Invalid value for Admin Status.'),
        ];
    }
}
