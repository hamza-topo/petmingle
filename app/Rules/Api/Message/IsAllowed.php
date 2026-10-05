<?php

namespace App\Rules\Api\Message;

use App\Services\InteractionPolicy;
use Illuminate\Contracts\Validation\Rule;

class IsAllowed implements Rule
{
    public function passes($attribute, $value)
    {
        $senderId = auth()->id();

        if (
            $senderId === null
            || !is_numeric($value)
        ) {
            return false;
        }

        return app(InteractionPolicy::class)
            ->canContactUsers(
                (int) $senderId,
                (int) $value
            );
    }

    public function message()
    {
        return __(
            'An active match is required and blocked accounts cannot contact each other.'
        );
    }
}
