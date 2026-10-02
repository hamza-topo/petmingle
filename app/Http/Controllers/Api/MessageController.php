<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Message\Store;
use App\Http\Requests\Api\Message\Update;
use App\Http\Resources\Api\Message\Chat;
use App\Reducer\Message\Conversation;
use App\Repositories\ConversationRepository;
use App\Repositories\MessageRepository;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function __construct(
        protected ConversationRepository $conversationRepository,
        protected MessageRepository $messageRepository
    ) {}
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return response()->json([
            'success' => true,
            'message' => \__('Messages has been fetched successfully.'),
            'data' => new Chat($this->messageRepository->messages(auth()->user()->id, $request->receiver_id))
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Store $request)
    {
        $data = $request->validated();

        $data['sender_id'] = $request->user()->id;

        if (empty($data['conversation_id'])) {
            $reducer = new Conversation();

            $conversation = $this->conversationRepository->create(
                $reducer->reduce($data)
            );

            if (!empty($conversation)) {
                $data['conversation_id'] = $conversation->id;
            }
        }

        return response()->json([
            'success' => true,
            'message' => __('Messages has been fetched successfully.'),
            'data' => $this->messageRepository->create($data),
        ]);
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Update $request, $id)
    {
        $message = $this->messageRepository->getById((int) $id);

        $this->authorize('update', $message);

        return response()->json([
            'success' => true,
            'message' => __('Messages has been modified successfully.'),
            'data' => $this->messageRepository->update((int) $id, $request->validated()),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
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

    /**
     * Restore the specified resource in storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
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
