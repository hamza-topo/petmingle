<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Auth\SignIn;
use App\Http\Requests\Api\Auth\SignUp;
use App\Repositories\AuthRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    public function __construct(protected AuthRepository $authRepository) {}
    public function login()
    {
        return view('auth.login');
    }

    public function signIn(SignIn $request)
    {
        $request->validate([
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => ['required', 'string'],
        ]);

        // Attempt to log the user in
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            // Authentication passed...
            return redirect()->intended('dashboard'); // Or any route you want
        }

        // If the login attempt was unsuccessful, return back to the login form with an error message.
        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->withInput();
    }

    public function register()
    {
        return view('auth.register');
    }

    public function signUp(SignUp $request)
    {
        try {
            $this->authRepository->signUp($request->all());
            
            //TODO::need email validation
            return redirect(route('user.register'));
        } catch (\Exception $e) {
            Log::error('error while creating new user account', [$e->getMessage()]);

            return redirect(route('user.register'));
        }
    }
}
