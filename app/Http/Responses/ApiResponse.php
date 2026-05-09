<?php

namespace App\Http\Responses;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\JsonResponse;

class ApiResponse
{
    public static function success(
        mixed $data = null,
        string $message = 'Success.',
        int $statusCode = 200
    ): JsonResponse {
        return response()->json([
            'success' => true,
            'status_code' => $statusCode,
            'message' => $message,
            'data' => $data,
        ], $statusCode);
    }

    public static function created(
        mixed $data = null,
        string $message = 'Created.'
    ): JsonResponse {
        return self::success($data, $message, 201);
    }

    public static function pagination(
        LengthAwarePaginator $paginator,
        string $message = 'Success.',
        string $dataKey = 'items'
    ): JsonResponse {
        return response()->json([
            'success' => true,
            'status_code' => 200,
            'message' => $message,
            'data' => [
                $dataKey => $paginator->items(),
            ],
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
            ],
        ], 200);
    }

    public static function error(
        string $message,
        int $statusCode = 400,
        array $errors = []
    ): JsonResponse {
        return response()->json([
            'success' => false,
            'status_code' => $statusCode,
            'message' => $message,
            'errors' => $errors,
        ], $statusCode);
    }

    public static function validation(array $errors, string $message = 'Validation failed.'): JsonResponse
    {
        return self::error($message, 422, $errors);
    }

    public static function unauthorized(string $message = 'Unauthorized.'): JsonResponse
    {
        return self::error($message, 401);
    }

    public static function forbidden(string $message = 'Forbidden.'): JsonResponse
    {
        return self::error($message, 403);
    }

    public static function notFound(string $message = 'Not found.'): JsonResponse
    {
        return self::error($message, 404);
    }
}