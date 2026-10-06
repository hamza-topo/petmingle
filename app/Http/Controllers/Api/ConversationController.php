<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Http\Requests\Api\Conversation\Index;
use App\Http\Resources\Api\Message\ConversationResource;
use App\Http\Responses\ApiResponse;
use App\Repositories\ConversationRepository;
use App\Repositories\MessageRepository;
use App\Services\InteractionPolicy;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;

class ConversationController extends Controller
{
    public function __construct(
        protected ConversationRepository $conversationRepository,
        protected MessageRepository $messageRepository,
        protected InteractionPolicy $interactionPolicy
    ) {}

    public function markSeen(
        Request $request,
        Conversation $conversation
    ) {
        $userId = (int) $request->user()->id;
        $firstUserId = (int) $conversation->first_user_id;
        $secondUserId = (int) $conversation->seconde_user_id;

        if (
            $firstUserId !== $userId
            && $secondUserId !== $userId
        ) {
            throw new AuthorizationException(
                'You are not a participant in this conversation.'
            );
        }

        $otherUserId =
            $firstUserId === $userId
                ? $secondUserId
                : $firstUserId;

        if (
            !$this->interactionPolicy->canContactUsers(
                $userId,
                $otherUserId
            )
        ) {
            throw new AuthorizationException(
                'This conversation is not available for contact.'
            );
        }

        $markedCount =
            $this->messageRepository
                ->markSeenInConversation(
                    (int) $conversation->id,
                    $userId
                );

        $unreadCount =
            $this->messageRepository
                ->unreadCountInConversation(
                    (int) $conversation->id,
                    $userId
                );

        return ApiResponse::success(
            [
                'conversation_id' => (int) $conversation->id,
                'marked_count' => $markedCount,
                'unread_count' => $unreadCount,
            ],
            __('Conversation marked as seen.')
        );
    }

    public function index(Index $request)
    {
        $validated = $request->validated();

        $perPage = (int) ($validated['per_page'] ?? 20);

        $conversations = $this
            ->conversationRepository
            ->paginateForUser(
                (int) $request->user()->id,
                $perPage,
                (int) ($validated['page'] ?? 1)
            );

        $conversations->appends([
            'per_page' => $perPage,
        ]);

        return ApiResponse::paginated(
            $conversations,
            ConversationResource::collection(
                $conversations->getCollection()
            )->resolve(),
            __('List of conversations.')
        );
    }
}
