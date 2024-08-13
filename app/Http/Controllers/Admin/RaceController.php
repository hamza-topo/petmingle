<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Repositories\RaceRepository;
use App\Repositories\SpeciesRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class RaceController extends Controller
{
    public function __construct(
        protected RaceRepository $raceRepository,
        protected SpeciesRepository $speciesRepository
    ) {
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.races.index', ['races' => $this->raceRepository->paginate()]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.races.create', [
            'species' => $this->speciesRepository->all()->pluck('name', 'id')
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $this->raceRepository->create($request->all());

            return redirect(route('admin.races.index'));
        } catch (\Exception $e) {
            Log::error('error Creating: ' . $e->getMessage());
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
        return view(
            'admin.races.show',
            ['race' => $this->raceRepository->getById($id)]
        );
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return view(
            'admin.races.edit',
            [
                'race' => $this->raceRepository->getById($id),
                'species' => $this->speciesRepository->all()->pluck('name', 'id')
            ]
        );
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
            $this->raceRepository->update($id, $request->all());

            return redirect(route('admin.races.index'));
        } catch (\Exception $e) {
            Log::error('error Updating: ' . $e->getMessage());
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
        //
    }
}
