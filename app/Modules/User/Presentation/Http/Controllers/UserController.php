<?php

namespace App\Modules\User\Presentation\Http\Controllers;

use App\Http\Controllers\Api\V1\ApiController;
use App\Http\Resources\Api\V1\User\UserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends ApiController
{
    public function me(Request $request): JsonResponse
    {
        return $this->success(UserResource::make($request->user()), 'OK');
    }
}
