<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Repositories\FilterRepository;
use Illuminate\Http\Response;

class FilterController extends Controller
{
    public function __construct(protected FilterRepository $filterRepository) {}

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        return response()->json([
            'success' => true,
            'message' => __('corresponding default filters of :'.auth()->user()->name),
            'data' => $this->filterRepository->all(),
        ]);
    }
}
