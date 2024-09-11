<?php

namespace App\Http\Requests\Web\Contact;

use Illuminate\Foundation\Http\FormRequest;

class Store extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     * @author Youssef Tamri <yousseftam100@gmail.com>
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:50',
            'subject' => 'required|string|max:100',
            'message' => 'required|string',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => __('The name field is required.'),
            'name.string' => __('The name must be a valid string.'),
            'name.max' => __('The name may not be greater than 100 characters.'),

            'email.required' => __('The email field is required.'),
            'email.email' => __('The email must be a valid email address.'),
            'email.max' => __('The email may not be greater than 50 characters.'),

            'subject.required' => __('The subject field is required.'),
            'subject.string' => __('The subject must be a valid string.'),
            'subject.max' => __('The subject may not be greater than 100 characters.'),

            'message.required' => __('The message field is required.'),
            'message.string' => __('The message must be a valid string.'),
        ];
    }
}
