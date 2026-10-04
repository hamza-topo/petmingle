<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Pet\Store;
use App\Repositories\PetRepository;
use App\Traits\ImageTrait;
use App\Http\Requests\Api\Pet\Update;
use App\Http\Resources\Api\PetResource;
use App\Http\Responses\ApiResponse;

class PetController extends Controller
{

    use ImageTrait;

    public function __construct(protected PetRepository $petRepository) {}
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return ApiResponse::success(
            PetResource::collection(
                $this->petRepository->all()
            )->resolve(),
            __('List of pets.')
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Store $request)
    {
        $pet = $request->all();

        $pet['user_id'] = $request->user()->id;

        $pet['images'] = $this->setFile(
            $request->file('images')
        )
            ->setName()
            ->upload();

        $createdPet = $this->petRepository->create($pet);

        return ApiResponse::created(
            (new PetResource($createdPet))->resolve(),
            __('Pet has been created.')
        );
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return ApiResponse::success(
            (new PetResource(
                $this->petRepository->getById((int) $id)
            ))->resolve(),
            __('Pet has been found.')
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\Api\Pet\Update  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
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

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $pet = $this->petRepository->getById((int) $id);

        $this->authorize('delete', $pet);

        return ApiResponse::success(
            $this->petRepository->delete((int) $id),
            __('Pet has been deleted successfully.')
        );
    }

    /**
     * Restore the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
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
