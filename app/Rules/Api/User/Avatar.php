<?php

namespace App\Rules\Api\User;

use Illuminate\Contracts\Validation\Rule;

class Avatar implements Rule
{
     /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return $value == auth()->user()->id;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return \__('The :attribute must match the authenticated user ID.');
    }
}
