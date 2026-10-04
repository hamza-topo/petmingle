<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Like\Store;
use App\Repositories\DislikeRepository;
use Illuminate\Http\Request;
use App\Http\Resources\Api\DislikeResource;
use App\Http\Responses\ApiResponse;

class DislikeController extends Controller
{
    public function __construct(protected DislikeRepository $dislikeRepository) {}

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $dislikes = $this->dislikeRepository->dislikes(
            auth()->user()->pet->id
        );

        return ApiResponse::paginated(
            $dislikes,
            DislikeResource::collection(
                $dislikes->getCollection()
            )->resolve(),
            __('List of dislikes.')
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
        $dislike = $this->dislikeRepository->create(
            $request->validated()
        );

        return ApiResponse::created(
            (new DislikeResource($dislike))->resolve(),
            __('Dislike has been created.')
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
