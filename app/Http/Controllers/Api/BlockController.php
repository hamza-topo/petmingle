<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Block\Store;
use App\Repositories\BlockRepository;
use App\Http\Resources\Api\BlockResource;
use App\Http\Responses\ApiResponse;


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
        return ApiResponse::success(
            BlockResource::collection(
                $this->blockRepository->blocks(
                    auth()->user()->id
                )
            )->resolve(),
            __('List of blocks.')
        );
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

        return ApiResponse::created(
            (new BlockResource($block))->resolve(),
            __('Block has been created.')
        );
    }
}
