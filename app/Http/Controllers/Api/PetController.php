<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Pet\Store;
use App\Repositories\PetRepository;
use App\Traits\ImageTrait;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PetController extends Controller
{

    use ImageTrait;

    public function __construct(protected PetRepository $petRepository)
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

            return response()->json([
                'success' => true,
                'message' => \__('List of pets.'),
                'data' => $this->petRepository->all()
            ]);
        } catch (\Exception $e) {
            dd($e->getMessage());
            return response()->json([
                'success' => false,
                'message' => \__('Sorry, cannot fetch pets.'),
                'trace' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
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
            $pet['images'] = $this->setFile($request->file('images'))
                ->setName()
                ->upload();
            return response()->json([
                'success' => true,
                'message' => \__('Pet has been created.'),
                'data' => $this->petRepository->create($pet)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => \__('Sorry, pet cannot be created out.'),
                'trace' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
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
        try {

            return response()->json([
                'success' => true,
                'message' => \__('Pet has been found.'),
                'data' => $this->petRepository->getById($id)
            ]);
        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => \__('Sorry, pet cannot be found.'),
                'trace' => $e->getMessage(),
            ], Response::HTTP_NOT_FOUND);
        }
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
        try {

            return response()->json([
                'success' => true,
                'message' => \__('Pet has been deleted successfully.'),
                'data' => $this->petRepository->delete($id)
            ]);
        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => \__('Sorry, pet cannot be delted.'),
                'trace' => $e->getMessage(),
            ], Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * Restore the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function restore($id)
    {
        try {

            return response()->json([
                'success' => true,
                'message' => \__('Pet has been restored successfully.'),
                'data' => $this->petRepository->restore($id)
            ]);
        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => \__('Sorry, pet cannot be restored.'),
                'trace' => $e->getMessage(),
            ], Response::HTTP_NOT_FOUND);
        }
    }
}
