<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\MatchResource;
use App\Http\Responses\ApiResponse;
use App\Repositories\MatchRepository;
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
}
