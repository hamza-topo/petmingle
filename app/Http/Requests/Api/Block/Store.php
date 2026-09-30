<?php

namespace App\Http\Requests\Api\Block;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class Store extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'to' => [
                'required',
                'integer',
                'exists:users,id',
                Rule::notIn([$this->user()?->id]),
            ],
            'cause' => ['nullable', 'integer'],
            'why' => ['nullable', 'string'],
        ];
    }
}