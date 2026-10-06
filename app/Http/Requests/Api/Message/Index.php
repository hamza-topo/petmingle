<?php

namespace App\Http\Requests\Api\Message;

use App\Rules\Api\Message\IsAllowed;
use Illuminate\Foundation\Http\FormRequest;

class Index extends FormRequest
{
    public function rules(): array
    {
        return [
            'receiver_id' => [
                'required',
                'integer',
                'exists:users,id',
                new IsAllowed,
            ],
            'page' => [
                'sometimes',
                'integer',
                'min:1',
            ],
            'per_page' => [
                'sometimes',
                'integer',
                'between:1,50',
            ],
            'sender_id' => ['prohibited'],
        ];
    }

    public function messages(): array
    {
        return [
            'receiver_id.required' => __(
                'The Field Receiver Id is required!'
            ),
            'receiver_id.integer' => __(
                'The Value of Receiver Id is invalid!'
            ),
            'receiver_id.exists' => __(
                'The selected receiver does not exist.'
            ),
            'page.integer' => __(
                'The message page must be a whole number.'
            ),
            'page.min' => __(
                'The message page must be at least 1.'
            ),
            'per_page.integer' => __(
                'The message page size must be a whole number.'
            ),
            'per_page.between' => __(
                'The message page size must be between 1 and 50.'
            ),
            'sender_id.prohibited' => __(
                'The sender is derived from authentication.'
            ),
        ];
    }
}
