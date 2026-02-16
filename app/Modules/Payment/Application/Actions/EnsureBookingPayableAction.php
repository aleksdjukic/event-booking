<?php

namespace App\Modules\Payment\Application\Actions;

use App\Modules\Booking\Domain\BookingTransitionGuard;
use App\Modules\Booking\Domain\Models\Booking;
use App\Modules\Payment\Domain\Repositories\PaymentRepositoryInterface;
use App\Modules\Shared\Domain\DomainError;
use App\Modules\Shared\Domain\DomainException;

class EnsureBookingPayableAction
{
    public function __construct(
        private readonly BookingTransitionGuard $bookingTransitionGuard,
        private readonly PaymentRepositoryInterface $paymentRepository,
    ) {
    }

    public function execute(Booking $booking): void
    {
        $bookingStatus = $booking->statusEnum();

        if (! $this->bookingTransitionGuard->canPay($bookingStatus)) {
            throw new DomainException(DomainError::INVALID_BOOKING_STATE_FOR_PAYMENT);
        }

        if ($this->paymentRepository->existsForBooking((int) $booking->{Booking::COL_ID})) {
            throw new DomainException(DomainError::PAYMENT_ALREADY_EXISTS);
        }
    }
}
