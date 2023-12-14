<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Message\Index;
use App\Http\Requests\Api\Message\Store;
use App\Http\Requests\Api\Message\Update;
use App\Http\Resources\Api\Message\Chat;
use App\Reducer\Message\Conversation;
use App\Repositories\ConversationRepository;
use App\Repositories\MessageRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class MessageController extends Controller
{
    public function __construct(
        protected ConversationRepository $conversationRepository,
        protected MessageRepository $messageRepository
    ) {
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {

            return response()->json([
                'success' => true,
                'message' => \__('Messages has been fetched successfully.'),
                'data' => new Chat($this->messageRepository->messages(auth()->user()->id, $request->receiver_id))
            ]);
        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => \__('Sorry, message cannot be fetched.'),
                'trace' => $e->getMessage(),
            ], Response::HTTP_NOT_FOUND);
        }
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
        try {
            $request = $request->all();
            if (empty($request['conversation_id'])) {
                $reducer = new Conversation();
                $conversation = $this->conversationRepository->create($reducer->reduce($request));
                if (!empty($conversation))
                    $request['conversation_id'] = $conversation->id;
            }
            return response()->json([
                'success' => true,
                'message' => \__('Messages has been fetched successfully.'),
                'data' => $this->messageRepository->create($request),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => \__('Sorry, message cannot be fetched.'),
                'trace' => $e->getMessage(),
            ]);
        }
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
        try {
            return response()->json([
                'success' => true,
                'message' => \__('Messages has been modified successfully.'),
                'data' => $this->messageRepository->update($id, $request->all()),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => \__('Sorry, message cannot be modified.'),
                'trace' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            return response()->json([
                'success' => true,
                'message' => \__('Messages has been deleted successfully.'),
                'data' => $this->messageRepository->delete($id),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => \__('Sorry, message cannot be deleted.'),
                'trace' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Restore the specified resource in storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function restore(int $id)
    {
        try {
            return response()->json([
                'success' => true,
                'message' => \__('Messages has been restored successfully.'),
                'data' => $this->messageRepository->restore($id),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => \__('Sorry, message cannot be restored.'),
                'trace' => $e->getMessage(),
            ]);
        }
    }
}
