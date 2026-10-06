<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Race\Store;
use App\Http\Requests\Api\Race\Update;
use App\Http\Requests\RaceRequest;
use App\Http\Resources\Api\RaceResource;
use App\Http\Responses\ApiResponse;
use App\Repositories\RaceRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class RaceController extends Controller
{
    public function __construct(protected RaceRepository $raceRepository)
    {
        $this->middleware('admin')->only(['store', 'update', 'destroy', 'restore']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        return ApiResponse::success(
            RaceResource::collection(
                $this->raceRepository->all()
            )->resolve(),
            __('List of races.')
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  RaceRequest  $request
     * @return Illuminate\Http\Response
     */
    public function store(Store $request)
    {
        $race = $this->raceRepository->create(
            $request->all()
        );

        return ApiResponse::created(
            (new RaceResource($race))->resolve(),
            __('Race has been created.')
        );
    }

    /**
     * Display the specified resource.
     *
     * @return Response
     */
    public function show(int $id)
    {
        return ApiResponse::success(
            (new RaceResource(
                $this->raceRepository->getById($id)
            ))->resolve(),
            __('Race has been found.')
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */
    public function update(Update $request, $id)
    {
        $race = $this->raceRepository->update(
            (int) $id,
            $request->all()
        );

        return ApiResponse::success(
            (new RaceResource($race))->resolve(),
            __('Race has been updated.')
        );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return Response
     */
    public function destroy(int $id)
    {
        return ApiResponse::success(
            $this->raceRepository->delete($id),
            __('Race has been deleted successfully.')
        );
    }

    /**
     * Restore the specified resource in storage.
     *
     * @return Response
     */
    public function restore(int $id)
    {
        return ApiResponse::success(
            $this->raceRepository->restore($id),
            __('Race has been restored successfully.')
        );
    }
}
