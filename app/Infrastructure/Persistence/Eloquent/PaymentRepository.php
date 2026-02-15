<?php

namespace App\Infrastructure\Persistence\Eloquent;

use App\Domain\Payment\Repositories\PaymentRepositoryInterface;
use App\Domain\Payment\Enums\PaymentStatus;
use App\Domain\Booking\Models\Booking;
use App\Domain\Payment\Models\Payment;

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
        $payment->{Payment::COL_BOOKING_ID} = $booking->id;
        $payment->{Payment::COL_AMOUNT} = round($amount, 2);
        $payment->{Payment::COL_STATUS} = $status;
        $payment->save();

        return $payment;
    }
}
