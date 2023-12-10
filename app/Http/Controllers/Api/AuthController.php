<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Auth\GetUser;
use App\Http\Requests\Api\Auth\Logout;
use App\Http\Requests\Api\Auth\SignIn;
use App\Http\Requests\Api\Auth\SignUp;
use App\Repositories\AuthRepository;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function __construct(protected AuthRepository $authRepository)
    {
    }


    public function signUp(SignUp $request)
    {
        try {
            $user = $this->authRepository->signUp($request->all());

            return response()->json([
                'success' => true,
                'message' => \__('User created successfully'),
                'data' => $user
            ], Response::HTTP_OK);
        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => \__('Enable to create this user'),
                'data' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function signIn(SignIn $request)
    {
        try {
            $credentials = $request->only(['email', 'password']);
            if (Auth::attempt($credentials)) {
                $user = Auth::user();
                $token = JWTAuth::fromUser($user);

                return response()->json(['token' => $token]);
            }

            return response()->json([
                'success' => false,
                'message' => \__('Login credentials are invalid.'),
            ], Response::HTTP_UNAUTHORIZED);
        } catch (JWTException $e) {

            return response()->json([
                'success' => false,
                'message' => 'Could not create token.',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function signOut(Logout $request)
    {
        try {
            JWTAuth::invalidate($request->token);

            return response()->json([
                'success' => true,
                'message' => \__('User has been logged out.')
            ]);
        } catch (JWTException $e) {
            return response()->json([
                'success' => false,
                'message' => \__('Sorry, user cannot be logged out.')
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function getUser(GetUser $request)
    {
        try {

            return response()->json(['user' => $this->authRepository->getUser($request->token)]);
        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => __('Sorry, user cannot be found.')
            ], Response::HTTP_NOT_FOUND);
        }
    }
}
