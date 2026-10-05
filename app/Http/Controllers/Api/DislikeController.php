<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Like\Store;
use App\Http\Resources\Api\DislikeResource;
use App\Http\Responses\ApiResponse;
use App\Repositories\DislikeRepository;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class DislikeController extends Controller
{
    public function __construct(
        protected DislikeRepository $dislikeRepository
    ) {}

    public function index()
    {
        $petId = $this->sourcePetId(request());

        $dislikes = $this->dislikeRepository->dislikes(
            $petId
        );

        return ApiResponse::paginated(
            $dislikes,
            DislikeResource::collection(
                $dislikes->getCollection()
            )->resolve(),
            __('List of dislikes.')
        );
    }

    public function create()
    {
        //
    }

    public function store(Store $request)
    {
        $dislike = $this->dislikeRepository->process(
            $this->sourcePetId($request),
            (int) $request->validated('to_pet_id')
        );

        return ApiResponse::success(
            (new DislikeResource($dislike))->resolve(),
            __('Dislike processed.')
        );
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        //
    }

    public function destroy($id)
    {
        //
    }

    private function sourcePetId(Request $request): int
    {
        $petId = $request->user()?->pet?->id;

        if ($petId === null) {
            throw ValidationException::withMessages([
                'pet' => [
                    __('Create a pet profile before interacting with pets.'),
                ],
            ]);
        }

        return (int) $petId;
    }
}
