<?php

namespace App\Modules\Payment\Infrastructure\Persistence\Eloquent;

use App\Modules\Payment\Domain\Repositories\PaymentRepositoryInterface;
use App\Modules\Payment\Domain\Enums\PaymentStatus;
use App\Modules\Booking\Domain\Models\Booking;
use App\Modules\Payment\Domain\Models\Payment;

class PaymentRepository implements PaymentRepositoryInterface
{
    public function findWithBooking(int $id): ?Payment
    {
        return Payment::query()->with(Payment::REL_BOOKING)->find($id);
    }

    public function existsForBooking(int $bookingId): bool
    {
        return Payment::query()->where(Payment::COL_BOOKING_ID, $bookingId)->exists();
    }

    public function create(Booking $booking, float $amount, PaymentStatus $status): Payment
    {
        $payment = new Payment();
        $payment->{Payment::COL_BOOKING_ID} = $booking->{Booking::COL_ID};
        $payment->{Payment::COL_AMOUNT} = round($amount, 2);
        $payment->{Payment::COL_STATUS} = $status;
        $payment->save();

        return $payment;
    }
}
