<?php

namespace App\Modules\Booking\Domain;

use App\Modules\Booking\Domain\Enums\BookingStatus;

class BookingTransitionGuard
{
    public function canCancel(BookingStatus $current): bool
    {
        return $current === BookingStatus::PENDING;
    }

    public function canPay(BookingStatus $current): bool
    {
        return $current === BookingStatus::PENDING;
    }
}
