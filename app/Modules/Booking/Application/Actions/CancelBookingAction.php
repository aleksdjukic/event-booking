<?php

namespace App\Modules\Booking\Application\Actions;

use App\Modules\Booking\Domain\BookingTransitionGuard;
use App\Modules\Booking\Domain\Enums\BookingStatus;
use App\Modules\Booking\Domain\Models\Booking;
use App\Modules\Booking\Domain\Repositories\BookingRepositoryInterface;
use App\Modules\Shared\Domain\DomainError;
use App\Modules\Shared\Domain\DomainException;

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

        $booking->{Booking::COL_STATUS} = BookingStatus::CANCELLED;

        return $this->bookingRepository->save($booking);
    }
}
