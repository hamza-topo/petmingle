<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Like\Store;
use App\Repositories\LikeRepository;
use Illuminate\Http\Request;
use App\Http\Resources\Api\LikeResource;
use App\Http\Responses\ApiResponse;

class LikeController extends Controller
{
    public function __construct(protected LikeRepository $likeRepository) {}

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $likes = $this->likeRepository->likes(
            auth()->user()->pet->id
        );

        return ApiResponse::paginated(
            $likes,
            LikeResource::collection(
                $likes->getCollection()
            )->resolve(),
            __('List of likes.')
        );
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
        $like = $this->likeRepository->create(
            $request->validated()
        );

        return ApiResponse::created(
            $like
                ? (new LikeResource($like))->resolve()
                : null,
            __('Like processed.')
        );
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
