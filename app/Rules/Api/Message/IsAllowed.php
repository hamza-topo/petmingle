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
        $senderPet = auth()->user()?->pet;

        $receiverPet = \App\Models\User::find($value)?->pet;

        if (!$senderPet || !$receiverPet) {
            return false;
        }

        return $this->likeRepository->isMatch([
            'from' => $senderPet->id,
            'to' => $receiverPet->id,
        ]);
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
