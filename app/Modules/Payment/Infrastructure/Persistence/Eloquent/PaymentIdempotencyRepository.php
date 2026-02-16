<?php

namespace App\Modules\Payment\Infrastructure\Persistence\Eloquent;

use App\Modules\Payment\Domain\Repositories\PaymentIdempotencyRepositoryInterface;
use App\Modules\Payment\Domain\Models\PaymentIdempotencyKey;

class PaymentIdempotencyRepository implements PaymentIdempotencyRepositoryInterface
{
    public function findForUserByKey(int $userId, string $idempotencyKey): ?PaymentIdempotencyKey
    {
        return PaymentIdempotencyKey::query()
            ->where(PaymentIdempotencyKey::COL_USER_ID, $userId)
            ->where(PaymentIdempotencyKey::COL_IDEMPOTENCY_KEY, $idempotencyKey)
            ->first();
    }

    public function createPending(int $userId, int $bookingId, string $idempotencyKey): PaymentIdempotencyKey
    {
        return PaymentIdempotencyKey::query()->firstOrCreate(
            [
                PaymentIdempotencyKey::COL_USER_ID => $userId,
                PaymentIdempotencyKey::COL_IDEMPOTENCY_KEY => $idempotencyKey,
            ],
            [
                PaymentIdempotencyKey::COL_BOOKING_ID => $bookingId,
                PaymentIdempotencyKey::COL_PAYMENT_ID => null,
            ]
        );
    }

    public function attachPayment(PaymentIdempotencyKey $record, int $paymentId): PaymentIdempotencyKey
    {
        $record->{PaymentIdempotencyKey::COL_PAYMENT_ID} = $paymentId;
        $record->save();

        return $record;
    }
}
