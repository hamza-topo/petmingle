<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Race\Store;
use App\Http\Requests\Api\Race\Update;
use App\Repositories\RaceRepository;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

class RaceController extends Controller
{

    public function __construct(protected RaceRepository $raceRepository)
    {
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            return $this->raceRepository->all();
        } catch (\Exception $e) {
            throw new HttpException(500, $e->getMessage());
        }
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\RaceRequest  $request
     * @return Illuminate\Http\Response
     */
    public function store(Store $request)
    {
        try {
            return $this->raceRepository->create($request->all());
        } catch (\Exception $e) {
            throw new HttpException(500, $e->getMessage());
        }
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(int $id)
    {
        try {
            return $this->raceRepository->getById($id);
        } catch (\Exception $e) {
            throw new HttpException(500, $e->getMessage());
        }
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
        try {
            return $this->raceRepository->update($id, $request->all());
        } catch (\Exception $e) {
            throw new HttpException(500, $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $id)
    {
        try {
            return $this->raceRepository->delete($id);
        } catch (\Exception $e) {
            throw new HttpException(500, $e->getMessage());
        }
    }

    /**
     * Restore the specified resource in storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function restore(int $id)
    {
        try {
            return $this->raceRepository->restore($id);
        } catch (\Exception $e) {
            throw new HttpException(500, $e->getMessage());
        }
    }
}
