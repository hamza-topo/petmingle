<?php

namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use App\Http\Requests\Web\User\Store;
use App\Http\Requests\Web\User\Update;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     * @author Youssef tamri <yousseftam100@gmail.com>
     * @return \Illuminate\Http\Response
     */

    public function __construct(protected UserRepository $userRepository) {}
    public function index()
    {
        return view('admin.users.index', ['users' => $this->userRepository->paginate(request()->get('paginate'))]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view(
            'admin.users.create',
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Store $request)
    {
        try {

            $this->userRepository->create($request->validated());

            return redirect(route('admin.users.index'))->with('success');
        } catch (\Exception $e) {
            Log::error('error while creating new user: ' . $e->getMessage());
            return redirect()->back();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(int $id)
    {
        $user = $this->userRepository->getById($id);
    
        return view('admin.users.show', compact('user'));
    }
    
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $user = $this->userRepository->getById($id); 
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Update $request, $id)
    {
        try {
            // Validate and update user using the repository
            $this->userRepository->update($id, $request->validated());

            return redirect()->route('admin.users.index')->with('success', __('User updated successfully.'));
        } catch (\Exception $e) {
            // Log the error and redirect back with failure
            Log::error('Error while updating user: ' . $e->getMessage());

            return redirect()->back()->withErrors(__('Error updating the user.'));
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $this->userRepository->delete($id);

            return redirect(route('admin.users.index'))->with('success');
        } catch (\Exception $e) {
            Log::erro('error occured while deleting this users: ' . $e->getMessage());
        }
    }

        /**
     * Restore the specified resource to storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function restore($id)
    {
        try {
            $this->userRepository->restore($id);

            return redirect(route('admin.users.index'))->with('success');
        } catch (\Exception $e) {
            Log::erro('error occured while restoring this users: ' . $e->getMessage());
        }
    }
}
