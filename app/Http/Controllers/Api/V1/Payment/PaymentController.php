<?php

namespace App\Http\Controllers\Api\V1\Payment;

use App\Application\Contracts\Services\PaymentTransactionServiceInterface;
use App\Http\Controllers\Api\V1\ApiController;
use App\Http\Requests\Api\V1\Payment\CreatePaymentRequest;
use App\Http\Requests\Api\V1\Payment\ShowPaymentRequest;
use App\Http\Resources\Api\V1\Payment\PaymentResource;
use App\Domain\Booking\Models\Booking;
use App\Domain\Payment\Models\Payment;
use Illuminate\Http\JsonResponse;

class PaymentController extends ApiController
{
    public function __construct(private readonly PaymentTransactionServiceInterface $paymentService)
    {
    }

    public function store(Booking $booking, CreatePaymentRequest $request): JsonResponse
    {
        $payment = $this->paymentService->process(
            $request->user(),
            $request->toDto($booking)
        );

        return $this->created(PaymentResource::make($payment), 'Payment processed successfully');
    }

    public function show(ShowPaymentRequest $request, Payment $payment): JsonResponse
    {
        return $this->success(PaymentResource::make($payment), 'OK');
    }
}
