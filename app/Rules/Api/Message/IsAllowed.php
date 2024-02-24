<?php

namespace App\Rules\Api\Message;

use App\Repositories\LikeRepository;
use App\Repositories\MatchRepository;
use Illuminate\Contracts\Validation\Rule;

class IsAllowed implements Rule
{

    protected $likeRepository;

    public function __construct()
    {
        $this->likeRepository = new LikeRepository();
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
        return $this->likeRepository->isMatch(['from'=> auth()->user()->id, 'to'=> $value]);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return \__('You can\'t send a message to user :attribute . No match');
    }
}
