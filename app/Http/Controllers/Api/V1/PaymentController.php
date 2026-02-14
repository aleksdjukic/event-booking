<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Payment\PaymentStoreRequest;
use App\Models\Payment;
use App\Services\Payment\PaymentTransactionService;
use App\Services\Support\ServiceException;
use App\Support\Http\ApiResponse;
use Illuminate\Http\JsonResponse;

class PaymentController extends Controller
{
    use ApiResponse;

    public function __construct(private readonly PaymentTransactionService $paymentService)
    {
    }

    public function store(int $id, PaymentStoreRequest $request): JsonResponse
    {
        $forceSuccess = $request->input('force_success') === null
            ? null
            : $request->boolean('force_success');

        try {
            $payment = $this->paymentService->process($request->user(), $id, $forceSuccess);
        } catch (ServiceException $exception) {
            return $this->error($exception->getMessage(), $exception->status());
        }

        return $this->created($payment, 'Payment processed successfully');
    }

    public function show(int $id): JsonResponse
    {
        $payment = $this->paymentService->find($id);

        if ($payment === null) {
            return $this->error('Payment not found.', 404);
        }

        $this->authorize('view', $payment);

        return $this->success($payment, 'OK');
    }
}
