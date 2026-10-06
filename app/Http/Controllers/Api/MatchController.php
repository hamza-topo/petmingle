<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\MatchResource;
use App\Http\Responses\ApiResponse;
use App\Repositories\MatchRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class MatchController extends Controller
{
    public function __construct(protected MatchRepository $matchRepository) {}

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function matches()
    {
        return ApiResponse::success(
            MatchResource::collection(
                $this->matchRepository->matches(
                    auth()->user()->pet->id
                )
            )->resolve(),
            __('List of matches.')
        );
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function mismatches()
    {
        return ApiResponse::success(
            MatchResource::collection(
                $this->matchRepository->mismatches(
                    auth()->user()->pet->id
                )
            )->resolve(),
            __('List of mismatches.')
        );
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        //
    }
}
