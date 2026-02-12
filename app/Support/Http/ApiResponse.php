<?php

namespace App\Support\Http;

use Illuminate\Http\JsonResponse;

trait ApiResponse
{
    public function success(mixed $data = null, ?string $message = 'OK', int $status = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
            'errors' => null,
        ], $status);
    }

    public function created(mixed $data = null, ?string $message = 'Created'): JsonResponse
    {
        return $this->success($data, $message, 201);
    }

    public function error(?string $message, int $status, mixed $errors = null): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'data' => null,
            'errors' => $errors,
        ], $status);
    }
}
