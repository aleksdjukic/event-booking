<?php

namespace App\Modules\Booking\Application\Services;

use App\Modules\Booking\Application\Actions\CancelBookingAction;
use App\Modules\Booking\Application\Actions\CreateBookingAction;
use App\Modules\Booking\Application\Actions\ListBookingsForUserAction;
use App\Modules\Booking\Application\Contracts\BookingServiceInterface;
use App\Modules\Booking\Application\DTO\CreateBookingData;
use App\Modules\Booking\Domain\Models\Booking;
use App\Modules\User\Domain\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;

class BookingService implements BookingServiceInterface
{
    public function __construct(
        private readonly CreateBookingAction $createBookingAction,
        private readonly CancelBookingAction $cancelBookingAction,
        private readonly ListBookingsForUserAction $listBookingsForUserAction,
    ) {
    }

    public function create(User $user, int $ticketId, CreateBookingData $data): Booking
    {
        return $this->createBookingAction->execute($user, $ticketId, $data);
    }

    /**
     * @return LengthAwarePaginator<int, Booking>
     */
    public function listFor(User $user): LengthAwarePaginator
    {
        return $this->listBookingsForUserAction->execute($user);
    }

    public function cancel(Booking $booking): Booking
    {
        return $this->cancelBookingAction->execute($booking);
    }
}
