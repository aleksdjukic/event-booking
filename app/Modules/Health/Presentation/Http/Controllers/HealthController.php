<?php

namespace App\Modules\Health\Presentation\Http\Controllers;

use App\Modules\Shared\Presentation\Http\Controllers\ApiController;
use Illuminate\Http\JsonResponse;

class HealthController extends ApiController
{
    public function ping(): JsonResponse
    {
        return $this->success(null, 'ok');
    }
}
