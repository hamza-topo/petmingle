<?php

namespace App\Repositories;

use App\Models\Conversation;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class ConversationRepository
{
    public function create(array $conversation): Conversation
    {
        if ($this->isNew($conversation)) {
            return Conversation::create($conversation);
        }

        return $this->getConversation($conversation)->first();
    }

    public function delete(array $conditions): int
    {
        return Conversation::where(
            $this->conditions($conditions)
        )
            ->orWhere(
                $this->conditions($conditions, true)
            )
            ->delete();
    }

    public function isNew(array $conditions = []): bool
    {
        return $this
            ->getConversation($conditions)
            ->isEmpty();
    }

    public function getConversation(
        array $conditions
    ): Collection {
        return Conversation::where(
            $this->conditions($conditions)
        )
            ->orWhere(
                $this->conditions($conditions, true)
            )
            ->get();
    }

    public function paginateForUser(
        int $userId,
        int $perPage = 20,
        int $page = 1
    ): LengthAwarePaginator {
        return Conversation::query()
            ->where(function ($query) use ($userId) {
                $query
                    ->where(
                        'first_user_id',
                        $userId
                    )
                    ->orWhere(
                        'seconde_user_id',
                        $userId
                    );
            })
            ->with([
                'firstUser.pet.race',
                'secondUser.pet.race',
                'latestMessage',
            ])
            ->withCount([
                'messages as unread_count' => fn ($query) => $query
                    ->where(
                        'receiver_id',
                        $userId
                    )
                    ->where(
                        'is_seen',
                        false
                    ),
            ])
            ->withMax(
                'messages',
                'created_at'
            )
            ->orderByDesc(
                'messages_max_created_at'
            )
            ->orderByDesc('id')
            ->paginate(
                $perPage,
                ['*'],
                'page',
                $page
            );
    }

    private function conditions(
        array $conditions,
        bool $flip = false
    ): array {
        return [
            'first_user_id' => $flip
                ? $conditions['seconde_user_id']
                : $conditions['first_user_id'],
            'seconde_user_id' => $flip
                ? $conditions['first_user_id']
                : $conditions['seconde_user_id'],
        ];
    }
}
