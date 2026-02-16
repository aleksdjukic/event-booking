<?php

namespace App\Modules\Payment\Domain\Repositories;

use App\Modules\Payment\Domain\Enums\PaymentStatus;
use App\Modules\Booking\Domain\Models\Booking;
use App\Modules\Payment\Domain\Models\Payment;

interface PaymentRepositoryInterface
{
    public function findWithBooking(int $id): ?Payment;

    public function existsForBooking(int $bookingId): bool;

    public function create(Booking $booking, float $amount, PaymentStatus $status): Payment;
}
