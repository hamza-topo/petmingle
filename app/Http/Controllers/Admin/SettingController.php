<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Repositories\UserRepository;
use App\Traits\ImageTrait;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class SettingController extends Controller
{
    /**
     * Image Trait
     */
    use ImageTrait;

    public function __construct(protected UserRepository $userRepository) {}

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        return view('admin.profile.index');
    }

    /**
     * Update the specified resource in storage.
     *
     * @return Response
     */
    public function update(Request $request)
    {
        try {
            $request = $request->all();
            if (! empty($request['avatar'])) {
                $request['avatar'] = $this->uploadAll([$request['avatar']]);
            }

            $this->userRepository->update(auth()->user()->id, $request);

            return redirect(route('admin.profile.index'));
        } catch (\Exception $e) {
            Log::error('Error while updating my profiel', [$e->getMessage()]);

            return redirect()->back();
        }
    }
}
