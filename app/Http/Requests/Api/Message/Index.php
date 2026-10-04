<?php

namespace App\Http\Requests\Api\Message;

use App\Rules\Api\Like\MatchUser;
use App\Rules\Api\Message\IsAllowed;
use Illuminate\Foundation\Http\FormRequest;

class Index extends FormRequest
{
      /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'sender_id' => ['required', 'integer', new MatchUser],
            'receiver_id' => ['required', 'integer', new IsAllowed],
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
            'sender_id.required' => \__('The Field Sender Id is required!'),
            'sender_id.integer' => \__('The Value of sender Id is invalid!'),
            'receiver_id.integer' => \__('The Value of Receiver Id is invalid!'),
            'receiver_id.integer' => \__('The Value of Receiver Id is invalid!'),
        ];
    }
}
