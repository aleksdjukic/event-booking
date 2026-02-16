<?php

namespace App\Modules\Auth\Presentation\Http\Controllers;

use App\Modules\Auth\Application\Contracts\AuthServiceInterface;
use App\Modules\Auth\Application\Services\AuthService;
use App\Modules\Shared\Presentation\Http\Controllers\ApiController;
use App\Modules\Auth\Presentation\Http\Requests\LoginRequest;
use App\Modules\Auth\Presentation\Http\Requests\RegisterRequest;
use App\Modules\User\Presentation\Http\Resources\UserResource;
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
            AuthService::PAYLOAD_USER => UserResource::make($payload[AuthService::PAYLOAD_USER]),
            AuthService::PAYLOAD_TOKEN => $payload[AuthService::PAYLOAD_TOKEN],
        ], 'Registered successfully');
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $payload = $this->authService->login($request->toDto());
        if ($payload === null) {
            return $this->error('Invalid credentials.', 401);
        }

        return $this->success([
            AuthService::PAYLOAD_USER => UserResource::make($payload[AuthService::PAYLOAD_USER]),
            AuthService::PAYLOAD_TOKEN => $payload[AuthService::PAYLOAD_TOKEN],
        ], 'Login successful');
    }

    public function logout(Request $request): JsonResponse
    {
        $this->authService->logout($request->user());

        return $this->success(null, 'Logout successful');
    }
}
