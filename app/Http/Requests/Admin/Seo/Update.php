<?php

namespace App\Http\Requests\Admin\Seo;

use App\Enums\App;
use Illuminate\Foundation\Http\FormRequest;

class Update extends FormRequest
{
    public function rules(): array
    {
        $locales = implode(',', App::LOCALES);

        return [
            'title' => ['required', 'array:'.$locales],
            'title.*' => ['nullable', 'string', 'max:255'],
            'meta' => ['required', 'array:description'],
            'meta.description' => ['required', 'array:'.$locales],
            'meta.description.*' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
