<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Adoption\Store;
use App\Models\Adoption;
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
     * @return Response
     */
    public function index()
    {
        $this->authorize('viewAny', Adoption::class);

        return view('admin.adoptions.index', [
            'adoptions' => $this->adoptionRepository->paginate(
                request()->get('paginate')
            ),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $this->authorize('create', Adoption::class);

        return view('admin.adoptions.create', [
            'users' => $this->plucker($this->userRepository->all(), 'name', 'Owner'),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(Store $request)
    {
        $this->authorize('create', Adoption::class);

        try {
            $this->adoptionRepository->create($request->all());

            return redirect()
                ->route('admin.adoptions.index')
                ->with('success', Response::HTTP_ACCEPTED);
        } catch (\Exception $e) {
            Log::error('error while saving the adoption:', $request->all());
            Log::error('error while saving the adoption:', [$e->getMessage()]);

            return redirect()
                ->route('admin.adoptions.create')
                ->withErrors(['error' => $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        $adoption = $this->adoptionRepository->getById((int) $id);

        $this->authorize('view', $adoption);

        return view('admin.adoptions.show', [
            'users' => $this->plucker($this->userRepository->all(), 'name', 'Owner'),
            'adoption' => $adoption,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        $adoption = $this->adoptionRepository->getById((int) $id);

        $this->authorize('view', $adoption);

        return view('admin.adoptions.edit', [
            'users' => $this->plucker($this->userRepository->all(), 'name', 'Owner'),
            'adoption' => $adoption,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        $adoption = $this->adoptionRepository->getById((int) $id);

        $this->authorize('update', $adoption);

        try {
            $this->adoptionRepository->update((int) $id, $request->all());

            return redirect()
                ->route('admin.adoptions.index')
                ->with('success', Response::HTTP_ACCEPTED);
        } catch (\Exception $e) {
            Log::error('error while saving the adoption:', $request->all());
            Log::error('error while saving the adoption:', [$e->getMessage()]);

            return redirect()
                ->route('admin.adoptions.edit', $id)
                ->withErrors(['error' => $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        $adoption = $this->adoptionRepository->getById((int) $id);

        $this->authorize('delete', $adoption);

        $this->adoptionRepository->delete((int) $id);

        return redirect()
            ->route('admin.adoptions.index')
            ->with('success', Response::HTTP_ACCEPTED);
    }
}
