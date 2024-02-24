<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Repositories\MatchRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class MatchController extends Controller
{

    public function __construct(protected MatchRepository $matchRepository)
    {
        
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function matches()
    {
        try {

            return response()->json([
                'success' => true,
                'message' => \__('corresponding matches of :' . auth()->user()->pet->name),
                'data' => $this->matchRepository->matches(auth()->user()->pet->id)
            ]);
        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => \__('Sorry, cannot fetch matches.'),
                'trace' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function mismatches()
    {
        try {

            return response()->json([
                'success' => true,
                'message' => \__('corresponding mismatches of :' . auth()->user()->pet->name),
                'data' => $this->matchRepository->mismatches(auth()->user()->pet->id)
            ]);
        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => \__('Sorry, cannot fetch mismatches.'),
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
    public function store(Request $request)
    {
        //
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
