<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Location\Near;
use App\Http\Requests\Api\Location\Store;
use App\Http\Resources\Api\Location\Near as LocationNear;
use App\Repositories\LocationRepository;
use App\Http\Resources\Api\LocationResource;
use App\Http\Responses\ApiResponse;

class LocationController extends Controller
{
    public function __construct(protected LocationRepository $locationRepository) {}

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return ApiResponse::success(
            LocationResource::collection(
                $this->locationRepository->forUser(
                    request()->user()->id
                )
            )->resolve(),
            __('List of locations.')
        );
    }

    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function near(Near $request)
    {
        return $this->nearbyResponse($request);
    }

    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function filter(Near $request)
    {
        return $this->nearbyResponse($request);
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

        $data['user_id'] = $request->user()->id;

        $location = $this->locationRepository->create($data);

        return ApiResponse::created(
            (new LocationResource($location))->resolve(),
            __('Location has been created.')
        );
    }

    /**
     * Display the specified resource.
     *
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
    public function show(string $id)
    {
        $location = $this->locationRepository->getById((int) $id);

        $this->authorize('view', $location);

        return ApiResponse::success(
            (new LocationResource($location))->resolve(),
            __('Location has been found.')
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Store $request, $id)
    {
        $location = $this->locationRepository->getById((int) $id);

        $this->authorize('update', $location);

        $updatedLocation = $this->locationRepository->update(
            (int) $id,
            $request->validated()
        );

        return ApiResponse::success(
            (new LocationResource($updatedLocation))->resolve(),
            __('Location has been updated successfully.')
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
        $location = $this->locationRepository->getById((int) $id);

        $this->authorize('delete', $location);

        return response()->json([
            'success' => true,
            'message' => __('Location has been deleted successfully.'),
            'data' => $this->locationRepository->delete((int) $id),
        ]);
    }

    /**
     * Restore the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function restore($id)
    {
        $location = $this->locationRepository->getByIdWithTrashed((int) $id);

        $this->authorize('restore', $location);

        return ApiResponse::success(
            $this->locationRepository->restore((int) $id),
            __('Location has been restored successfully.')
        );
    }

    private function nearbyResponse(Near $request)
    {
        $user = $request->user();

        if (!$user->pet) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'pet' => [
                    __('Create a pet profile before using Discovery.'),
                ],
            ]);
        }

        $origin = $this->locationRepository->currentForUser(
            $user->id
        );

        if (!$origin) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'location' => [
                    __('Set a valid account location before using Discovery.'),
                ],
            ]);
        }

        $validated = $request->validated();

        $resources = $this->locationRepository->nearbyForUser(
            requesterUserId: $user->id,
            latitude: (float) $origin->latitude,
            longitude: (float) $origin->longitude,
            radiusKm: (int) ($validated['radius_km'] ?? 5),
        );

        return ApiResponse::success(
            (new LocationNear($resources))->resolve(),
            __('List of locations near you.')
        );
    }
}
