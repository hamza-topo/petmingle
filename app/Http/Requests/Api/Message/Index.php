<?php

namespace App\Http\Requests\Api\Message;

use App\Rules\Api\Message\IsAllowed;
use Illuminate\Foundation\Http\FormRequest;

class Index extends FormRequest
{
    public function rules()
    {
        return [
            'receiver_id' => [
                'required',
                'integer',
                'exists:users,id',
                new IsAllowed,
            ],
            'sender_id' => ['prohibited'],
        ];
    }

    public function messages()
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
            'sender_id.prohibited' => __(
                'The sender is derived from authentication.'
            ),
        ];
    }
}
