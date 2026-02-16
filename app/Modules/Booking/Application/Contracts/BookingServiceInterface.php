<?php

namespace App\Modules\Booking\Application\Contracts;

use App\Modules\Booking\Application\DTO\CreateBookingData;
use App\Domain\Booking\Models\Booking;
use App\Domain\User\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;

interface BookingServiceInterface
{
    public function create(User $user, int $ticketId, CreateBookingData $data): Booking;

    /**
     * @return LengthAwarePaginator<int, Booking>
     */
    public function listFor(User $user): LengthAwarePaginator;

    public function cancel(Booking $booking): Booking;
}
