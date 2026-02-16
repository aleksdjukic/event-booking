<?php

namespace App\Modules\Auth\Presentation\Http\Controllers;

use App\Application\Contracts\Services\AuthServiceInterface;
use App\Http\Controllers\Api\V1\ApiController;
use App\Http\Requests\Api\V1\Auth\LoginRequest;
use App\Http\Requests\Api\V1\Auth\RegisterRequest;
use App\Http\Resources\Api\V1\User\UserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends ApiController
{
    public function __construct(private readonly AuthServiceInterface $authService)
    {
    }

    public function register(RegisterRequest $request): JsonResponse
    {
        $payload = $this->authService->register($request->toDto());

        return $this->created([
            'user' => UserResource::make($payload['user']),
            'token' => $payload['token'],
        ], 'Registered successfully');
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $payload = $this->authService->login($request->toDto());
        if ($payload === null) {
            return $this->error('Invalid credentials.', 401);
        }

        return $this->success([
            'user' => UserResource::make($payload['user']),
            'token' => $payload['token'],
        ], 'Login successful');
    }

    public function logout(Request $request): JsonResponse
    {
        $this->authService->logout($request->user());

        return $this->success(null, 'Logout successful');
    }
}
