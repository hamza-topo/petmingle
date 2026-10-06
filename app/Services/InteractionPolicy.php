<?php

namespace App\Services;

use App\Models\Block;
use App\Models\Like;
use App\Models\MatchTable;
use App\Models\Pet;
use App\Models\User;

final class InteractionPolicy
{
    public function isBlockedBetween(
        int $firstUserId,
        int $secondUserId
    ): bool {
        if ($firstUserId === $secondUserId) {
            return false;
        }

        return Block::query()
            ->where(function ($query) use (
                $firstUserId,
                $secondUserId
            ) {
                $query
                    ->where([
                        'from' => $firstUserId,
                        'to' => $secondUserId,
                    ])
                    ->orWhere([
                        'from' => $secondUserId,
                        'to' => $firstUserId,
                    ]);
            })
            ->exists();
    }

    public function canLikePet(
        int $actorUserId,
        int $targetPetId
    ): bool {
        $targetUserId = Pet::query()
            ->whereKey($targetPetId)
            ->value('user_id');

        if ($targetUserId === null) {
            return false;
        }

        $targetUserId = (int) $targetUserId;

        return $actorUserId !== $targetUserId
            && ! $this->isBlockedBetween(
                $actorUserId,
                $targetUserId
            );
    }

    public function canMatchPets(
        int $firstPetId,
        int $secondPetId
    ): bool {
        if ($firstPetId === $secondPetId) {
            return false;
        }

        $pets = Pet::query()
            ->whereIn('id', [$firstPetId, $secondPetId])
            ->get(['id', 'user_id'])
            ->keyBy('id');

        if (
            ! $pets->has($firstPetId)
            || ! $pets->has($secondPetId)
        ) {
            return false;
        }

        $firstUserId = (int) $pets[$firstPetId]->user_id;
        $secondUserId = (int) $pets[$secondPetId]->user_id;

        if (
            $this->isBlockedBetween(
                $firstUserId,
                $secondUserId
            )
        ) {
            return false;
        }

        return Like::query()
            ->where([
                'from' => $firstPetId,
                'to' => $secondPetId,
            ])
            ->exists()
            && Like::query()
                ->where([
                    'from' => $secondPetId,
                    'to' => $firstPetId,
                ])
                ->exists();
    }

    public function canContactUsers(
        int $firstUserId,
        int $secondUserId
    ): bool {
        if (
            $firstUserId === $secondUserId
            || $this->isBlockedBetween(
                $firstUserId,
                $secondUserId
            )
        ) {
            return false;
        }

        $firstPetId = User::query()
            ->find($firstUserId)
            ?->pet
            ?->id;

        $secondPetId = User::query()
            ->find($secondUserId)
            ?->pet
            ?->id;

        if (
            $firstPetId === null
            || $secondPetId === null
        ) {
            return false;
        }

        return MatchTable::query()
            ->where([
                'from' => $firstPetId,
                'to' => $secondPetId,
            ])
            ->exists()
            && MatchTable::query()
                ->where([
                    'from' => $secondPetId,
                    'to' => $firstPetId,
                ])
                ->exists();
    }
}
