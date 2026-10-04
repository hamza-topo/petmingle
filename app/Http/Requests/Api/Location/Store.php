<?php

namespace App\Http\Requests\Api\Location;

use Illuminate\Foundation\Http\FormRequest;

class Store extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
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
            'latitude.required' => __('The Latitude is required.'),
            'latitude.numeric' => __('The Latitude must be numeric.'),
            'latitude.between' => __('The Latitude must be between -90 and 90 degrees.'),

            'longitude.required' => __('The Longitude is required.'),
            'longitude.numeric' => __('The Longitude must be numeric.'),
            'longitude.between' => __('The Longitude must be between -180 and 180 degrees.'),
        ];
    }

}
