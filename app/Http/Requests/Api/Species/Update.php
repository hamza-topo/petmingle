<?php

namespace App\Http\Requests\Api\Species;

use Illuminate\Foundation\Http\FormRequest;

class Update extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|max:50',
        ];;
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'name.required' => \__('The Field Species Name is required.'),
            'name.max' => \__('This Species Name is too much long.'),
        ];
    }

}
