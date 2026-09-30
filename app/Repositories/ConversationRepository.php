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

    public function delete(array $conditions): int
    {
        return Conversation::where($this->conditions($conditions))
            ->orWhere($this->conditions($conditions, true))
            ->delete();
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
