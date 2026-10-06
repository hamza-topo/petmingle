<?php

namespace App\Http\Controllers\Api;

use App\Events\IsWritingEvent;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Message\Index;
use App\Http\Requests\Api\Message\Store;
use App\Http\Requests\Api\Message\Typing;
use App\Http\Requests\Api\Message\Update;
use App\Http\Resources\Api\Message\MessageResource;
use App\Http\Responses\ApiResponse;
use App\Reducer\Message\Conversation;
use App\Repositories\ConversationRepository;
use App\Repositories\MessageRepository;

class MessageController extends Controller
{
    public function __construct(
        protected ConversationRepository $conversationRepository,
        protected MessageRepository $messageRepository
    ) {}

    public function index(Index $request)
    {
        $validated = $request->validated();
        $receiverId = (int) $validated['receiver_id'];
        $perPage = (int) ($validated['per_page'] ?? 30);
        $page = (int) ($validated['page'] ?? 1);

        $messages = $this->messageRepository->messagesBetween(
            (int) $request->user()->id,
            $receiverId,
            $perPage,
            $page
        );

        $messages->appends([
            'receiver_id' => $receiverId,
            'per_page' => $perPage,
        ]);

        return ApiResponse::paginated(
            $messages,
            MessageResource::collection(
                $messages->getCollection()
            )->resolve(),
            __('Message thread.')
        );
    }

    public function store(Store $request)
    {
        $data = $request->validated();

        $data['sender_id'] = $request->user()->id;

        if (empty($data['conversation_id'])) {
            $reducer = new Conversation;

            $conversation = $this->conversationRepository->create(
                $reducer->reduce($data)
            );

            if (! empty($conversation)) {
                $data['conversation_id'] = $conversation->id;
            }
        }

        return response()->json([
            'success' => true,
            'message' => __('Messages has been fetched successfully.'),
            'data' => $this->messageRepository->create($data),
        ]);
    }

    public function typing(Typing $request)
    {
        $data = $request->validated();

        IsWritingEvent::dispatch(
            (int) $request->user()->id,
            (int) $data['receiver_id'],
            (bool) $data['is_writing']
        );

        return ApiResponse::success(
            [
                'receiver_user_id' => (int) $data['receiver_id'],
                'is_writing' => (bool) $data['is_writing'],
            ],
            __('Typing state accepted.')
        );
    }

    public function update(Update $request, $id)
    {
        $message = $this->messageRepository->getById((int) $id);

        $this->authorize('update', $message);

        return response()->json([
            'success' => true,
            'message' => __('Messages has been modified successfully.'),
            'data' => $this->messageRepository->update(
                (int) $id,
                $request->validated()
            ),
        ]);
    }

    public function destroy($id)
    {
        $message = $this->messageRepository->getById((int) $id);

        $this->authorize('delete', $message);

        return response()->json([
            'success' => true,
            'message' => __('Messages has been deleted successfully.'),
            'data' => $this->messageRepository->delete((int) $id),
        ]);
    }

    public function restore(int $id)
    {
        $message = $this->messageRepository->getByIdWithTrashed($id);

        $this->authorize('restore', $message);

        return response()->json([
            'success' => true,
            'message' => __('Messages has been restored successfully.'),
            'data' => $this->messageRepository->restore($id),
        ]);
    }
}
