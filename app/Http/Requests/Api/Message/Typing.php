<?php

namespace App\Http\Requests\Api\Message;

use App\Rules\Api\Message\IsAllowed;
use Illuminate\Foundation\Http\FormRequest;

class Typing extends FormRequest
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
            'is_writing' => [
                'required',
                'boolean',
            ],
            'sender_id' => ['prohibited'],
        ];
    }
}
