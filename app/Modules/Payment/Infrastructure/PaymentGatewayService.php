<?php

namespace App\Modules\Payment\Infrastructure;

use App\Modules\Booking\Domain\Models\Booking;
use App\Modules\Payment\Domain\Services\PaymentGatewayInterface;

class PaymentGatewayService implements PaymentGatewayInterface
{
    private const SIMULATED_SUCCESS_RATE = 80;

    public function process(Booking $booking, ?bool $forceSuccess = null): bool
    {
        if ($forceSuccess !== null) {
            return $forceSuccess;
        }

        return $this->simulateGatewayResult($booking);
    }

    private function simulateGatewayResult(Booking $booking): bool
    {
        $seed = crc32(implode('|', [
            (string) $booking->{Booking::COL_ID},
            (string) $booking->{Booking::COL_TICKET_ID},
            (string) $booking->{Booking::COL_USER_ID},
            (string) $booking->{Booking::COL_QUANTITY},
        ]));

        return ($seed % 100) < self::SIMULATED_SUCCESS_RATE;
    }
}
