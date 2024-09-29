<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Pet\Store;
use App\Repositories\PetRepository;
use App\Repositories\RaceRepository;
use App\Repositories\SpeciesRepository;
use App\Repositories\UserRepository;
use App\Traits\ImageTrait;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PetController extends Controller
{

    /**
     * Image Trait
     */
    use ImageTrait;
    /**
     * constructor
     *
     * @param PetRepository $petRepository
     * @param UserRepository $userRepository
     * @param SpeciesRepository $speciesRepository
     * @param RaceRepository $raceRepository
     */
    public function __construct(
        protected PetRepository $petRepository,
        protected UserRepository $userRepository,
        protected SpeciesRepository $speciesRepository,
        protected RaceRepository $raceRepository,
    ) {
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.pets.index', ['pets' => $this->petRepository->paginate(request()->get('paginate'))]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view(
            'admin.pets.create',
            [
                'users' => $this->plucker($this->userRepository->all(), 'name', 'Owner'),
                'species' => $this->plucker($this->speciesRepository->all(), 'name', 'Species'),
                'races' => $this->plucker($this->raceRepository->getAllFromCache(), 'name', 'Race'),
            ]
        );
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
            $pet = $request->all();
            if(!empty($request->file('images'))){
                $pet['images'] = $this->setFile($request->file('images'))
                ->setName()
                ->upload();
            }
            $pet = $this->petRepository->create($pet);

            return redirect()->route('admin.pets.index')->with('success', Response::HTTP_ACCEPTED);
        } catch (\Exception $e) {
            Log::error('error while saving the pet:', $request->all());
            Log::error('error while saving the pet:', [$e->getMessage()]);

            return redirect()->route('admin.pets.create')
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
        $pet = $this->petRepository->getById($id);
        return view(
            'admin.pets.show',
            [
                'users' => $this->plucker($this->userRepository->all(), 'name', 'Owner'),
                'species' => $this->plucker($this->speciesRepository->all(), 'name', 'Species'),
                'races' => $this->plucker($this->raceRepository->getAllFromCache(), 'name', 'Race'),
                'pet' => $pet,
            ]
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
        $pet = $this->petRepository->getById($id);
        return view(
            'admin.pets.edit',
            [
                'users' => $this->plucker($this->userRepository->all(), 'name', 'Owner'),
                'species' => $this->plucker($this->speciesRepository->all(), 'name', 'Species'),
                'races' => $this->plucker($this->raceRepository->getAllFromCache(), 'name', 'Race'),
                'pet' => $pet,
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
            $pet = $request->all();
            if(!empty($request->file('images'))){
                $pet['images'] = $this->setFile($request->file('images'))
                ->setName()
                ->upload();
            }
           
            $pet = $this->petRepository->update($id, $pet);

            return redirect()->route('admin.pets.index')->with('success', Response::HTTP_ACCEPTED);
        } catch (\Exception $e) {
            Log::error('error while saving the pet:', $request->all());
            Log::error('error while saving the pet:', [$e->getMessage()]);

            return redirect()->route('admin.pets.edit', $id)
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

    /**
     * Method to pluck given peroperty
     *
     * @param Collection $colletion
     * @param string $key
     * @return Collection
     */
    protected function plucker(Collection $colletion, string $key, string $value = ''): Collection
    {
        return $colletion->pluck($key, 'id')->prepend('Please select an: '. $value, '');
    }
}
