<?php

namespace App\Http\Requests\Admin\Seo;

use App\Enums\Pages;
use Illuminate\Validation\Rule;

class Store extends Update
{
    public function rules(): array
    {
        return parent::rules() + [
            'key' => ['required', Rule::enum(Pages::class), Rule::unique('seos', 'key')->whereNull('deleted_at')],
        ];
    }
}
