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

    public function __construct(protected LangService $langService)
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
                'message' => \__('Languages has been fetched successfully.'),
                'data' => App::LOCALES
            ]);
        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => \__('Sorry, Languages cannot be fetched.'),
                'trace' => $e->getMessage(),
            ], Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function current()
    {
        try {

            return response()->json([
                'success' => true,
                'message' => \__('Current language has been fetched successfully.'),
                'data' => $this->langService->current()
            ]);
        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => \__('Sorry, Current Language cannot be fetched.'),
                'trace' => $e->getMessage(),
            ], Response::HTTP_NOT_FOUND);
        }
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function set(SetLanguage $request)
    {
        try {

            return response()->json([
                'success' => true,
                'message' => \__('Language '. $request->lang .' has been modified successfully.'),
                'data' => $this->langService->set($request->lang)
            ]);
        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => \__('Sorry, Language cannot be modified.'),
                'trace' => $e->getMessage(),
            ], Response::HTTP_NOT_FOUND);
        }
    }
}
