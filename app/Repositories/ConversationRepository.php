<?php

namespace App\Repositories;

use App\Models\Conversation;
use \Illuminate\Database\Eloquent\Collection;

class ConversationRepository
{

    public function create(array $conversation): Conversation
    {
        if ($this->isNew($conversation))
            return Conversation::create($conversation);
        return $this->getConversation($conversation)->first();
    }

    public function delete(array $condition): void
    {
        //TODO: This conception will lead to a probleme
        //more fields need to be added (delete for first_user, but should be accessible for seconde user)
    }

    public function isNew(array $conditions = []): bool
    {
        $isNew = $this->getConversation($conditions);

        return $isNew->count() > 0 ? false : true;
    }

    public function getConversation(array $conditions): Collection
    {
        return Conversation::where($this->conditions($conditions))
            ->orWhere($this->conditions($conditions, true))
            ->get();
    }

    private function conditions(array $conditions, bool $flip = false): array
    {
        return [
            'first_user_id' => $flip === false ? $conditions['first_user_id'] : $conditions['seconde_user_id'],
            'seconde_user_id' => $flip === false ? $conditions['seconde_user_id'] : $conditions['first_user_id'],
        ];
    }
}
