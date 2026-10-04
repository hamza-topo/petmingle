<?php

namespace App\Http\Requests\Api\Location;

use Illuminate\Foundation\Http\FormRequest;

class Near extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'user_id' => 'required|integer',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'perimetre' => 'integer',
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
            'user_id.required' => \__('The Field User Id is required!'),
            'user_id.integer' => \__('The Value of user is invalid!'),
            'latitude.required' => \__('Turn on your Localisation.'),
            'longitude.between' => \__('Turn on your Localisation'),
            'latitude.between' => \__('The latitude must be between -90 and 90 degrees.'),
            'longitude.between' => \__('The longitude must be between -180 and 180 degrees.'),
            'perimetre.integer' => \__('The Perimetre of user is invalid!'),
        ];
    }
}
