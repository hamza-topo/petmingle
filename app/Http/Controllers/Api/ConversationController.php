<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Conversation\Index;
use App\Http\Resources\Api\Message\ConversationResource;
use App\Http\Responses\ApiResponse;
use App\Repositories\ConversationRepository;

class ConversationController extends Controller
{
    public function __construct(
        protected ConversationRepository $conversationRepository
    ) {}

    public function index(Index $request)
    {
        $validated = $request->validated();

        $conversations = $this
            ->conversationRepository
            ->paginateForUser(
                (int) $request->user()->id,
                (int) ($validated['per_page'] ?? 20),
                (int) ($validated['page'] ?? 1)
            );

        return ApiResponse::paginated(
            $conversations,
            ConversationResource::collection(
                $conversations->getCollection()
            )->resolve(),
            __('List of conversations.')
        );
    }
}
