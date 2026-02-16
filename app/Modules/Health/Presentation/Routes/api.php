<?php

use App\Modules\Health\Presentation\Http\Controllers\HealthController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function (): void {
    Route::get('/ping', [HealthController::class, 'ping']);
});
