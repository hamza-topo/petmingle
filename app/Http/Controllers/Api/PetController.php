<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Pet\Store;
use App\Http\Requests\Api\Pet\Update;
use App\Http\Resources\Api\PetResource;
use App\Http\Responses\ApiResponse;
use App\Repositories\PetRepository;
use App\Services\PetImageStorage;
use Throwable;

class PetController extends Controller
{
    public function __construct(
        protected PetRepository $petRepository,
        protected PetImageStorage $petImageStorage,
    ) {}

    public function index()
    {
        return ApiResponse::success(
            PetResource::collection(
                $this->petRepository->all()
            )->resolve(),
            __('List of pets.')
        );
    }

    public function store(Store $request)
    {
        $data = $request->validated();

        unset(
            $data['image'],
            $data['size'],
            $data['traits'],
            $data['energy'],
            $data['playdate'],
            $data['images']
        );

        $data['user_id'] = $request->user()->id;
        $data['images'] = [];

        $newImage = null;

        if ($request->hasFile('image')) {
            $newImage = $this->petImageStorage->store(
                $request->file('image')
            );

            $data['images'] = [$newImage];
        }

        try {
            $createdPet = $this->petRepository->create($data);
        } catch (Throwable $exception) {
            if ($newImage !== null) {
                $this->petImageStorage->delete([$newImage]);
            }

            throw $exception;
        }

        return ApiResponse::created(
            (new PetResource($createdPet))->resolve(),
            __('Pet has been created.')
        );
    }

    public function show($id)
    {
        return ApiResponse::success(
            (new PetResource(
                $this->petRepository->getById((int) $id)
            ))->resolve(),
            __('Pet has been found.')
        );
    }

    public function statistics($id)
    {
        $pet = $this->petRepository->getById((int) $id);

        $this->authorize('view', $pet);

        return ApiResponse::success(
            [
                'matches' => $pet->matches()->count(),
                'likes_sent' => $pet->likes()->count(),
            ],
            __('Pet profile statistics.')
        );
    }

    public function update(Update $request, $id)
    {
        $pet = $this->petRepository->getById((int) $id);

        $this->authorize('update', $pet);

        $data = $request->validated();
        $oldImages = is_array($pet->images) ? $pet->images : [];

        unset(
            $data['image'],
            $data['remove_image'],
            $data['images'],
            $data['user_id']
        );

        $newImage = null;
        $mediaChanged = false;

        if ($request->hasFile('image')) {
            $newImage = $this->petImageStorage->store(
                $request->file('image')
            );

            $data['images'] = [$newImage];
            $mediaChanged = true;
        } elseif ($request->boolean('remove_image')) {
            $data['images'] = [];
            $mediaChanged = true;
        }

        try {
            $updatedPet = $this->petRepository->update(
                (int) $id,
                $data
            );
        } catch (Throwable $exception) {
            if ($newImage !== null) {
                $this->petImageStorage->delete([$newImage]);
            }

            throw $exception;
        }

        if ($mediaChanged) {
            $this->petImageStorage->delete($oldImages);
        }

        return ApiResponse::success(
            (new PetResource($updatedPet))->resolve(),
            __('Pet has been updated successfully.')
        );
    }

    public function destroy($id)
    {
        $pet = $this->petRepository->getById((int) $id);

        $this->authorize('delete', $pet);

        return ApiResponse::success(
            $this->petRepository->delete((int) $id),
            __('Pet has been deleted successfully.')
        );
    }

    public function restore($id)
    {
        $pet = $this->petRepository->getByIdWithTrashed((int) $id);

        $this->authorize('restore', $pet);

        return ApiResponse::success(
            $this->petRepository->restore((int) $id),
            __('Pet has been restored successfully.')
        );
    }
}
