<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Species\Store;
use App\Http\Requests\Api\Species\Update;
use App\Repositories\SpeciesRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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
        return view(
            'admin.species.index',
            [
                'species' => $this->speciesRepository->paginate(request()->get('paginate'))
            ]
        );
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.species.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Store $request)
    {
        try {

            $this->speciesRepository->create($request->all());

            return redirect(route('admin.species.index'))->with('success');
        } catch (\Exception $e) {
            Log::error('error while creating new species: ' . $e->getMessage());

            return redirect()->back();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return view('admin.species.show', [
            'species' => $this->speciesRepository->getById($id),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return view('admin.species.edit', [
            'species' => $this->speciesRepository->getById($id),
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
        try {
            $this->speciesRepository->update($id, $request->all());

            return redirect(route('admin.species.index'));
        } catch (\Exception $e) {
            Log::error('error while updating the species: ' . $e->getMessage());
            return redirect()->back();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $this->speciesRepository->delete($id);

            return redirect(route('admin.species.index'))->with('success');
        } catch (\Exception $e) {
            Log::erro('error occured while deleting this species: ' . $e->getMessage());
        }
    }
}
