<?php

namespace App\Http\Requests\Api\Auth;

use App\Rules\Api\User\Avatar as UserAvatar;
use Illuminate\Foundation\Http\FormRequest;

class Avatar extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'id' => ['required', new UserAvatar],
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
            'id.required' => \__('The User Id is required.'),
        ];
    }
}
