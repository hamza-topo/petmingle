<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Block\Store;
use App\Repositories\BlockRepository;

class BlockController extends Controller
{

    public function __construct(protected BlockRepository $blockRepository) {}
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json([
            'success' => true,
            'message' => \__('corresponding List of Blocks of :' . auth()->user()->name),
            'data' => $this->blockRepository->blocks(auth()->user()->id)
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\Api\Block\Store  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Store $request)
    {
        $data = $request->validated();

        $data['from'] = $request->user()->id;

        $block = $this->blockRepository->create($data);

        return response()->json([
            'success' => true,
            'message' => __('Block ok'),
            'data' => $block,
        ]);
    }
}
