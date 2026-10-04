<?php

namespace App\Http\Requests\Api\Languages;

use App\Enums\App;
use Illuminate\Foundation\Http\FormRequest;

class SetLanguage extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'lang' => 'required|in:' . \implode(',', App::LOCALES),
        ];
    }
    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'lang.required' => \__('The Field Language Id is required!'),
            'lang.in' => \__('The Value of Language is invalid!'),
        ];
    }

}
