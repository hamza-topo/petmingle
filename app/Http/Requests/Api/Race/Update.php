<?php

namespace App\Http\Requests\Api\Race;

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
            'name' => 'required|unique:races|max:50',
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
            'name.required' => \__('The Field Race Name is required.'),
            'name.unique' => \__('The Race Name already exist.'),
            'name.max' => \__('This Race Name is too much long.'),
        ];
    }
}
