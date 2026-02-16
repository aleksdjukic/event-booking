<?php

namespace App\Modules\Payment\Application\Actions;

use App\Modules\Payment\Domain\Models\PaymentIdempotencyKey;
use App\Modules\Payment\Domain\Repositories\PaymentIdempotencyRepositoryInterface;

class AttachPaymentToIdempotencyRecordAction
{
    public function __construct(private readonly PaymentIdempotencyRepositoryInterface $idempotencyRepository)
    {
    }

    public function execute(PaymentIdempotencyKey $idempotencyRecord, int $paymentId): void
    {
        $this->idempotencyRepository->attachPayment($idempotencyRecord, $paymentId);
    }
}
