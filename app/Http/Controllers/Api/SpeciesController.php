<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Species\Store;
use App\Http\Requests\Api\Species\Update;
use App\Repositories\SpeciesRepository;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

class SpeciesController extends Controller
{
    public function __construct(protected SpeciesRepository $speciesRepository)
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
            return $this->speciesRepository->all();
        } catch (\Exception $e) {
            throw new HttpException(500, $e->getMessage());
        }
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\Request  $request
     * @return Illuminate\Http\Response
     */
    public function store(Store $request)
    {
        try {
            return $this->speciesRepository->create($request->all());
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
            return $this->speciesRepository->getById($id);
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
            return $this->speciesRepository->update($id, $request->all());
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
            return $this->speciesRepository->delete($id);
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
            return $this->speciesRepository->restore($id);
        } catch (\Exception $e) {
            throw new HttpException(500, $e->getMessage());
        }
    }
}
