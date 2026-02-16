<?php

namespace App\Modules\Shared\Presentation\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Support\Http\ApiResponder;
use Illuminate\Http\JsonResponse;

abstract class ApiController extends Controller
{
    protected function success(mixed $data = null, string $message = 'OK', int $status = 200): JsonResponse
    {
        return app(ApiResponder::class)->success($data, $message, $status);
    }

    protected function created(mixed $data = null, string $message = 'Created'): JsonResponse
    {
        return app(ApiResponder::class)->created($data, $message);
    }

    protected function error(string $message, int $status, mixed $errors = null): JsonResponse
    {
        return app(ApiResponder::class)->error($message, $status, $errors);
    }
}
