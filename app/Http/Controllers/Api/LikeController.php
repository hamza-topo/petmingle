<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Like\Store;
use App\Http\Resources\Api\LikeResource;
use App\Http\Responses\ApiResponse;
use App\Repositories\LikeRepository;
use App\Services\InteractionPolicy;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class LikeController extends Controller
{
    public function __construct(
        protected LikeRepository $likeRepository,
        protected InteractionPolicy $interactionPolicy
    ) {}

    public function index()
    {
        $petId = $this->sourcePetId(request());

        $likes = $this->likeRepository->likes(
            $petId
        );

        return ApiResponse::paginated(
            $likes,
            LikeResource::collection(
                $likes->getCollection()
            )->resolve(),
            __('List of likes.')
        );
    }

    public function create()
    {
        //
    }

    public function store(Store $request)
    {
        $targetPetId = (int) $request->validated(
            'to_pet_id'
        );

        if (
            !$this->interactionPolicy->canLikePet(
                (int) $request->user()->id,
                $targetPetId
            )
        ) {
            throw ValidationException::withMessages([
                'to_pet_id' => [
                    __('This pet is not available for interaction.'),
                ],
            ]);
        }

        $like = $this->likeRepository->process(
            $this->sourcePetId($request),
            $targetPetId
        );

        return ApiResponse::success(
            (new LikeResource($like))->resolve(),
            __('Like processed.')
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
