<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Species\Store;
use App\Http\Requests\Api\Species\Update;
use App\Repositories\SpeciesRepository;
use App\Http\Resources\Api\SpeciesResource;
use App\Http\Responses\ApiResponse;

class SpeciesController extends Controller
{
    public function __construct(protected SpeciesRepository $speciesRepository)
    {
        $this->middleware('admin')->only(['store', 'update', 'destroy', 'restore']);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return ApiResponse::success(
            SpeciesResource::collection(
                $this->speciesRepository->all()
            )->resolve(),
            __('List of species.')
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\Request  $request
     * @return Illuminate\Http\Response
     */
    public function store(Store $request)
    {
        $species = $this->speciesRepository->create(
            $request->validated()
        );

        return ApiResponse::created(
            (new SpeciesResource($species))->resolve(),
            __('Species has been created.')
        );
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(int $id)
    {
        return ApiResponse::success(
            (new SpeciesResource(
                $this->speciesRepository->getById($id)
            ))->resolve(),
            __('Species has been found.')
        );
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
        $species = $this->speciesRepository->update(
            (int) $id,
            $request->validated()
        );

        return ApiResponse::success(
            (new SpeciesResource($species))->resolve(),
            __('Species has been updated.')
        );
    }

    /**
     * Destroy the specified resource in storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function destroy(int $id)
    {
        return ApiResponse::success(
            $this->speciesRepository->delete($id),
            __('Species has been deleted successfully.')
        );
    }

    /**
     * Restore the specified resource in storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function restore(int $id)
    {
        return ApiResponse::success(
            $this->speciesRepository->restore($id),
            __('Species has been restored successfully.')
        );
    }
}
