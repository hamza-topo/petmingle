<?php

namespace App\Http\Requests\Api\Message;

use App\Rules\Api\Message\IsAllowed;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class Store extends FormRequest
{
    public function rules()
    {
        return [
            'receiver_id' => ['required', 'integer', new IsAllowed],
            'content' => 'required|string|max:1000',
        ];
    }

    public function messages()
    {
        return [
            'receiver_id.required' => __('The Field Receiver Id is required!'),
            'receiver_id.integer' => __('The Value of Receiver Id is invalid!'),
            'content.required' => __('The Field Content is required!'),
            'content.max' => __('The Field Content is too long!'),
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Validation errors',
            'data' => $validator->errors(),
        ]));
    }
}