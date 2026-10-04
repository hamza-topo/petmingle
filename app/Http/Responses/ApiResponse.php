<?php

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;

final class ApiResponse
{
    public static function success(
        mixed $data,
        string $message,
        int $status = 200
    ): JsonResponse {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $status);
    }

    public static function paginated(
        LengthAwarePaginator $paginator,
        array $data,
        string $message
    ): JsonResponse {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
            ],
            'links' => [
                'first' => $paginator->url(1),
                'last' => $paginator->url($paginator->lastPage()),
                'prev' => $paginator->previousPageUrl(),
                'next' => $paginator->nextPageUrl(),
            ],
        ]);
    }

    public static function created(
        mixed $data,
        string $message
    ): JsonResponse {
        return self::success(
            $data,
            $message,
            201
        );
    }
}
