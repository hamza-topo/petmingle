<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Adoption\Store;
use App\Repositories\AdoptionRepository;
use App\Repositories\RaceRepository;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class AdoptionController extends Controller
{
    public function __construct(
        protected AdoptionRepository $adoptionRepository,
        protected UserRepository $userRepository,
        protected RaceRepository $raceRepository
    ) {}
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.adoptions.index', ['adoptions' => $this->adoptionRepository->paginate(request()->get('paginate'))]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.adoptions.create', [
            'users' => $this->plucker($this->userRepository->all(), 'name', 'Owner'),
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
        try {
            $this->adoptionRepository->create($request->all());

            return redirect()->route('admin.adoptions.index')->with('success', Response::HTTP_ACCEPTED);
        } catch (\Exception $e) {
            Log::error('error while saving the adoption:', $request->all());
            Log::error('error while saving the adoption:', [$e->getMessage()]);

            return redirect()->route('admin.adoptions.create')
                ->withErrors(['error' => $e->getMessage()])->withInput();
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
        $adoption = $this->adoptionRepository->getById($id);

        return view('admin.adoptions.show', [
            'users' => $this->plucker($this->userRepository->all(), 'name', 'Owner'),
            'adoption' => $adoption
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
        $adoption = $this->adoptionRepository->getById($id);

        return view('admin.adoptions.edit', [
            'users' => $this->plucker($this->userRepository->all(), 'name', 'Owner'),
            'adoption' => $adoption
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
            $this->adoptionRepository->update($id, $request->all());

            return redirect()->route('admin.adoptions.index')->with('success', Response::HTTP_ACCEPTED);
        } catch (\Exception $e) {
            Log::error('error while saving the adoption:', $request->all());
            Log::error('error while saving the adoption:', [$e->getMessage()]);

            return redirect()->route('admin.adoptions.edit', $id)
                ->withErrors(['error' => $e->getMessage()])->withInput();
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
