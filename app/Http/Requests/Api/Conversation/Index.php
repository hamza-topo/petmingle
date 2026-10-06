<?php

namespace App\Http\Requests\Api\Conversation;

use Illuminate\Foundation\Http\FormRequest;

class Index extends FormRequest
{
    public function rules(): array
    {
        return [
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
            'user_id' => ['prohibited'],
        ];
    }

    public function messages(): array
    {
        return [
            'page.integer' => __(
                'The conversation page must be a whole number.'
            ),
            'page.min' => __(
                'The conversation page must be at least 1.'
            ),
            'per_page.integer' => __(
                'The conversation page size must be a whole number.'
            ),
            'per_page.between' => __(
                'The conversation page size must be between 1 and 50.'
            ),
            'user_id.prohibited' => __(
                'Conversation identity is derived from authentication.'
            ),
        ];
    }
}
