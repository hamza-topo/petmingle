<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Pet\Store;
use App\Http\Requests\Api\Pet\Update;
use App\Http\Resources\Api\PetResource;
use App\Http\Responses\ApiResponse;
use App\Repositories\PetRepository;
use App\Traits\ImageTrait;
use RuntimeException;

class PetController extends Controller
{
    use ImageTrait;

    public function __construct(protected PetRepository $petRepository) {}

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

        if ($request->hasFile('image')) {
            $storedImage = $this->setFile(
                $request->file('image')
            )
                ->setName()
                ->upload();

            if ($storedImage === false) {
                throw new RuntimeException(
                    'Pet image upload failed.'
                );
            }

            $data['images'] = [$storedImage];
        }

        $createdPet = $this->petRepository->create($data);

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

    public function update(Update $request, $id)
    {
        $pet = $this->petRepository->getById((int) $id);

        $this->authorize('update', $pet);

        $data = $request->validated();

        if ($request->hasFile('images')) {
            $data['images'] = $this->setFile($request->file('images'))
                ->setName()
                ->upload();
        }

        unset($data['user_id']);

        $updatedPet = $this->petRepository->update(
            (int) $id,
            $data
        );

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
