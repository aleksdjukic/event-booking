<?php

namespace App\Services;

use App\Models\Booking;

class PaymentService
{
    public function process(Booking $booking, bool $forceSuccess = true): bool
    {
        return $forceSuccess;
    }
}
