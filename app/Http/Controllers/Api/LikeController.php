<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Like\Store;
use App\Repositories\LikeRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class LikeController extends Controller
{
    public function __construct(protected LikeRepository $likeRepository)
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
                'message' => \__('corresponding Likes of :' . auth()->user()->pet->name),
                'data' => $this->likeRepository->likes(auth()->user()->pet->id)
            ]);
        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => \__('Sorry, cannot fetch pets.'),
                'trace' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
            //After storing check if there is the inverse in db then create a matche;
            $like = $this->likeRepository->create($request->all());
            
            return response()->json([
                'success' => true,
                'message' => \__('like ok'),
                'data' => $like
            ]);
        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => \__('Sorry, cannot fetch pets.'),
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
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        //
    }
}
