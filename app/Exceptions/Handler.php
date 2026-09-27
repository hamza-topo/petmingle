<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function render($request, Throwable $e)
    {
        if (!$request->is('api/*')) {
            return parent::render($request, $e);
        }

        return match (true) {
            $e instanceof AuthenticationException =>
            $this->apiError('Unauthenticated.', 401),

            $e instanceof AuthorizationException,
            $e instanceof AccessDeniedHttpException =>
            $this->apiError('Forbidden.', 403),

            $e instanceof ValidationException =>
            response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $e->errors(),
            ], $e->status),

            $e instanceof ModelNotFoundException,
            $e instanceof NotFoundHttpException =>
            $this->apiError('Resource not found.', 404),

            $e instanceof HttpExceptionInterface =>
            $this->renderApiHttpException($e),

            default =>
            $this->apiError('Internal server error.', 500),
        };
    }

    private function renderApiHttpException(HttpExceptionInterface $e): JsonResponse
    {
        $status = $e->getStatusCode();

        $message = match ($status) {
            401 => 'Unauthenticated.',
            403 => 'Forbidden.',
            404 => 'Resource not found.',
            422 => 'Validation failed.',
            default => $status >= 500
                ? 'Internal server error.'
                : 'Request failed.',
        };

        return $this->apiError($message, $status);
    }

    private function apiError(string $message, int $status): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
        ], $status);
    }
}
