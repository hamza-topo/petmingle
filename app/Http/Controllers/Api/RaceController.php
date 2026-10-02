<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Race\Store;
use App\Http\Requests\Api\Race\Update;
use App\Repositories\RaceRepository;

class RaceController extends Controller
{

    public function __construct(protected RaceRepository $raceRepository)
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
        return $this->raceRepository->all();
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\RaceRequest  $request
     * @return Illuminate\Http\Response
     */
    public function store(Store $request)
    {
        return $this->raceRepository->create($request->all());
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(int $id)
    {
        return $this->raceRepository->getById($id);
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
        return $this->raceRepository->update($id, $request->all());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $id)
    {
        return $this->raceRepository->delete($id);
    }

    /**
     * Restore the specified resource in storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function restore(int $id)
    {
        return $this->raceRepository->restore($id);
    }
}
