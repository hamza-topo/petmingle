<?php

namespace App\Http\Controllers\Api;

use App\Enums\App;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Languages\SetLanguage;
use App\Services\LangService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class LangController extends Controller
{
    public function __construct(protected LangService $langService) {}

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        return response()->json([
            'success' => true,
            'message' => \__('Languages has been fetched successfully.'),
            'data' => App::LOCALES,
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @return Response
     */
    public function current()
    {
        return response()->json([
            'success' => true,
            'message' => \__('Current language has been fetched successfully.'),
            'data' => $this->langService->current(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function set(SetLanguage $request)
    {
        return response()->json([
            'success' => true,
            'message' => \__('Language '.$request->lang.' has been modified successfully.'),
            'data' => $this->langService->set($request->lang),
        ]);
    }
}
