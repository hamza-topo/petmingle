<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Pet\Store;
use App\Repositories\PetRepository;
use App\Traits\ImageTrait;
use Illuminate\Http\Request;

class PetController extends Controller
{

    use ImageTrait;

    public function __construct(protected PetRepository $petRepository) {}
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        return response()->json([
            'success' => true,
            'message' => \__('List of pets.'),
            'data' => $this->petRepository->all()
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
        $pet = $request->all();
        $pet['images'] = $this->setFile($request->file('images'))
            ->setName()
            ->upload();
        return response()->json([
            'success' => true,
            'message' => \__('Pet has been created.'),
            'data' => $this->petRepository->create($pet)
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return response()->json([
            'success' => true,
            'message' => \__('Pet has been found.'),
            'data' => $this->petRepository->getById($id)
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
        return response()->json([
            'success' => true,
            'message' => \__('Pet has been deleted successfully.'),
            'data' => $this->petRepository->delete($id)
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
        return response()->json([
            'success' => true,
            'message' => \__('Pet has been restored successfully.'),
            'data' => $this->petRepository->restore($id)
        ]);
    }
}
