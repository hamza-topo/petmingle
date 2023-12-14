<?php

namespace App\Reducer\Message;

class Conversation
{

    public array $data = [];

    public function reduce(array $request): array
    {
        $this->data =  [
            'first_user_id' => $request['sender_id'],
            'seconde_user_id' => $request['receiver_id'],
        ];

        return $this->data;
    }
}
