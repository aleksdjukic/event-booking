<?php

namespace App\Application\Booking\Actions;

use App\Domain\Booking\BookingTransitionGuard;
use App\Domain\Booking\Enums\BookingStatus;
use App\Domain\Booking\Models\Booking;
use App\Domain\Booking\Repositories\BookingRepositoryInterface;
use App\Domain\Shared\DomainError;
use App\Domain\Shared\DomainException;

class CancelBookingAction
{
    public function __construct(
        private readonly BookingTransitionGuard $transitionGuard,
        private readonly BookingRepositoryInterface $bookingRepository,
    ) {
    }

    public function execute(Booking $booking): Booking
    {
        $currentStatus = $booking->statusEnum();

        if (! $this->transitionGuard->canCancel($currentStatus)) {
            throw new DomainException(DomainError::BOOKING_NOT_PENDING);
        }

        $booking->status = BookingStatus::CANCELLED;

        return $this->bookingRepository->save($booking);
    }
}
