<?php

namespace App\Http\Resources\Api\Message;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ConversationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => (int) $this->id,
            'current_user_id' => (int) $request->user()->id,
            'participants' => [
                $this->participant($this->firstUser),
                $this->participant($this->secondUser),
            ],
            'last_message' => $this->latestMessage
                ? (new MessageResource(
                    $this->latestMessage
                ))->toArray($request)
                : null,
            'unread_count' => (int) $this->unread_count,
        ];
    }

    private function participant(?User $user): ?array
    {
        if ($user === null) {
            return null;
        }

        $pet = $user->pet;

        return [
            'user_id' => (int) $user->id,
            'name' => $user->name,
            'pet' => $pet
                ? [
                    'id' => (int) $pet->id,
                    'user_id' => (int) $pet->user_id,
                    'name' => $pet->name,
                    'species_id' => (int) $pet->species_id,
                    'race' => $pet->race
                        ? [
                            'id' => (int) $pet->race->id,
                            'species_id' => (int) $pet->race->species_id,
                            'name' => $pet->race->name,
                        ]
                        : null,
                    'age_years' => (int) $pet->age,
                    'sex' => $pet->sexe !== null
                        ? (int) $pet->sexe
                        : null,
                    'images' => $pet->images ?? [],
                ]
                : null,
        ];
    }
}
