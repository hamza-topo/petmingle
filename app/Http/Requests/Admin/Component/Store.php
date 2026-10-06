<?php

namespace App\Http\Requests\Admin\Component;

use App\Enums\Component;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class Store extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', Rule::enum(Component::class)],
            'title' => ['sometimes', 'array'],
            'title.*' => ['nullable', 'string', 'max:255'],
            'content' => ['sometimes', 'array'],
            'content.*' => ['array'],
            'content.*.*' => ['nullable', 'string', 'max:10000'],
            'media' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:5120'],
        ];
    }
}
