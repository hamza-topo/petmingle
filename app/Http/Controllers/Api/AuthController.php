<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Auth\SignIn;
use App\Http\Requests\Api\Auth\SignUp;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use App\Repositories\AuthRepository;
use App\Traits\ImageTrait;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Laravel\Socialite\Facades\Socialite;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    use ImageTrait;

    public function __construct(protected AuthRepository $authRepository) {}

    /**
     * SignUp Method.
     *
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function signUp(SignUp $request): Response
    {
        $user = $request->validated();
        $user['avatar'] = $this->setFile($request->file('avatar'))
            ->setName()
            ->upload();
        $user = $this->authRepository->signUp($user);

        return response()->json([
            'success' => true,
            'message' => \__('User created successfully'),
            'data' => $user,
        ], Response::HTTP_OK);
    }

    /**
     * SignIn Method.
     *
     * @return \Illuminate\Http\Response
     */
    public function signIn(SignIn $request): Response
    {
        $credentials = $request->only(['email', 'password']);

        if (! Auth::attempt($credentials)) {
            return response()->json([
                'success' => false,
                'message' => __('Login credentials are invalid.'),
            ], Response::HTTP_UNAUTHORIZED);
        }

        /** @var User $user */
        $user = Auth::user();

        $token = $user->createToken('api')->plainTextToken;

        return response()->json([
            'success' => true,
            'token' => $token,
            'token_type' => 'Bearer',
        ]);
    }

    public function me(Request $request): Response
    {
        $user = $request->user();
        $pet = $user->pet;

        return response()->json([
            'success' => true,
            'message' => __('Authenticated identity.'),
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                ],
                'pet' => $pet ? [
                    'id' => $pet->id,
                    'user_id' => $pet->user_id,
                    'name' => $pet->name,
                ] : null,
            ],
        ])->header('Cache-Control', 'private, no-store');
    }

    public function signOut(Request $request): Response
    {
        $request->user()->currentAccessToken()?->delete();

        return response()->json([
            'success' => true,
            'message' => __('User has been logged out.'),
        ]);
    }

    /**
     * Redirect the user to the Provider authentication page.
     *
     * @param  string  $provider  given provider
     * @return JsonResponse
     */
    public function redirectToProvider(string $provider)
    {
        $this->validateProvider($provider);

        return Socialite::driver($provider)->stateless()->redirect();
    }

    /**
     * Obtain the user information from Provider.
     *
     * @return JsonResponse
     */
    public function handleProviderCallback($provider)
    {
        $this->validateProvider($provider);

        try {
            $providerUser = Socialite::driver($provider)->stateless()->user();

            $user = $this->authRepository->firstOrCreateProviderUser(
                $providerUser->user,
                $provider
            );

            Auth::login($user, true);

            if (request()->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => __('User logged in successfully'),
                    'data' => $user,
                ], Response::HTTP_OK);
            }

            return redirect()->intended(RouteServiceProvider::HOME);
        } catch (ClientException $e) {
            Log::error($e->getMessage());

            throw ValidationException::withMessages([
                'provider' => [
                    'Invalid credentials provided.',
                ],
            ]);
        }
    }

    public function removeAvatar(Request $request, int $id): Response
    {
        $user = $this->authRepository->getById($id);

        $this->authorize('removeAvatar', $user);

        return response()->json([
            'success' => true,
            'message' => __('Avatar has been removed successfully.'),
            'data' => $this->authRepository->removeAvatar($id),
        ]);
    }

    public function enable(Request $request, int $id): Response
    {
        $user = $this->authRepository->getByIdWithTrashed($id);

        $this->authorize('enable', $user);

        $this->authRepository->restore($id);

        return response()->json([
            'success' => true,
            'message' => __('Account has been enabled successfully.'),
        ]);
    }

    public function disable(Request $request, int $id): Response
    {
        $user = $this->authRepository->getById($id);

        $this->authorize('disable', $user);

        $user->tokens()->delete();

        $this->authRepository->delete($id);

        return response()->json([
            'success' => true,
            'message' => __('Account has been disabled successfully.'),
        ]);
    }

    /**
     * @return JsonResponse
     */
    protected function validateProvider($provider)
    {
        if (! in_array($provider, ['facebook', 'github', 'google'], true)) {
            throw ValidationException::withMessages([
                'provider' => [
                    'Please login using facebook, github or google.',
                ],
            ]);
        }
    }
}
