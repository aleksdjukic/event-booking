<?php

use App\Modules\Payment\Presentation\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function (): void {
    Route::middleware(['auth:sanctum', 'role:admin,customer'])->group(function (): void {
        Route::post('/bookings/{booking}/payment', [PaymentController::class, 'store']);
        Route::get('/payments/{payment}', [PaymentController::class, 'show']);
    });
});
