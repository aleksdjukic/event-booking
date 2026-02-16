<?php

namespace App\Modules\Payment\Domain\Services;

use App\Modules\Booking\Domain\Models\Booking;

interface PaymentGatewayInterface
{
    public function process(Booking $booking, ?bool $forceSuccess = null): bool;
}
