<?php

namespace App\Http\Controllers\Api;

use App\Filters\PetFilter;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Location\Near;
use App\Http\Requests\Api\Location\Store;
use App\Http\Resources\Api\Location\Near as LocationNear;
use App\Repositories\LocationRepository;
use Illuminate\Http\Request;

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
        return response()->json([
            'success' => true,
            'message' => \__('List of Locations.'),
            'data' => $this->locationRepository->forUser(request()->user()->id)
        ]);
    }
    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function near(Near $request)
    {
        $resources = $this->locationRepository->near($request->all());

        return response()->json([
            'success' => true,
            'message' => \__('List of Locations nears to you.'),
            'data' => new LocationNear($resources),
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function filter(Request $request)
    {
        $coordinates = [
            'latitude' => $request->filters['latitude'],
            'longitude' => $request->filters['longitude'],
            'perimetre' => $request->filters['perimetre'],
            'user_id' => $request->user_id ?? auth()->user()->id,
        ];

        $resources = $this->locationRepository->near($coordinates);
        $petFilter = new PetFilter;
        $resources = $petFilter->filter($resources, $request->all());

        return response()->json([
            'success' => true,
            'message' => \__('List of Locations nears to you.'),
            'data' => new LocationNear($resources),
        ]);
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

        return response()->json([
            'success' => true,
            'message' => __('Location has been created.'),
            'data' => $this->locationRepository->create($data),
        ]);
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

        return response()->json([
            'success' => true,
            'message' => __('Location has been found.'),
            'data' => $location,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
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

        return response()->json([
            'success' => true,
            'message' => __('Location has been restored successfully.'),
            'data' => $this->locationRepository->restore((int) $id),
        ]);
    }
}
