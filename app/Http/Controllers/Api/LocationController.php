<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Location\Near;
use App\Http\Requests\Api\Location\Store;
use App\Http\Resources\Api\Location\Near as LocationNear;
use App\Http\Resources\Api\LocationResource;
use App\Http\Responses\ApiResponse;
use App\Repositories\LocationRepository;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;

class LocationController extends Controller
{
    public function __construct(protected LocationRepository $locationRepository) {}

    /**
     * Display a listing of the resource.
     *
     * @return Response
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
     * Display nearby Discovery results.
     *
     * @return Response
     */
    public function near(Near $request)
    {
        return $this->nearbyResponse($request);
    }

    /**
     * Legacy route using the same normalized Discovery contract.
     *
     * @return Response
     */
    public function filter(Near $request)
    {
        return $this->nearbyResponse($request);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
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
     * @return Response
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
     * @return Response
     */
    public function update(Store $request, $id)
    {
        $location = $this->locationRepository->getById((int) $id);

        $this->authorize('update', $location);

        $updatedLocation = $this->locationRepository->update(
            (int) $id,
            $request->validated() + ['label' => null]
        );

        return ApiResponse::success(
            (new LocationResource($updatedLocation))->resolve(),
            __('Location has been updated successfully.')
        );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return Response
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
     * @return Response
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

        if (! $user->pet) {
            throw ValidationException::withMessages([
                'pet' => [
                    __('Create a pet profile before using Discovery.'),
                ],
            ]);
        }

        $origin = $this->locationRepository->currentForUser(
            $user->id
        );

        if (! $origin) {
            throw ValidationException::withMessages([
                'location' => [
                    __('Set a valid account location before using Discovery.'),
                ],
            ]);
        }

        $validated = $request->validated();

        $radiusKm = (int) ($validated['radius_km'] ?? 5);
        $perPage = (int) ($validated['per_page'] ?? 24);
        $page = (int) ($validated['page'] ?? 1);
        $speciesId = isset($validated['species_id'])
            ? (int) $validated['species_id']
            : null;
        $raceId = isset($validated['race_id'])
            ? (int) $validated['race_id']
            : null;

        $paginator = $this->locationRepository->nearbyForUser(
            requesterUserId: $user->id,
            latitude: (float) $origin->latitude,
            longitude: (float) $origin->longitude,
            radiusKm: $radiusKm,
            perPage: $perPage,
            page: $page,
            speciesId: $speciesId,
            raceId: $raceId,
        );

        $query = [
            'radius_km' => $radiusKm,
            'per_page' => $perPage,
        ];

        if ($speciesId !== null) {
            $query['species_id'] = $speciesId;
        }

        if ($raceId !== null) {
            $query['race_id'] = $raceId;
        }

        $paginator->appends($query);

        return ApiResponse::paginated(
            $paginator,
            (new LocationNear(
                $paginator->getCollection()
            ))->resolve(),
            __('Nearby pets.')
        );
    }
}
