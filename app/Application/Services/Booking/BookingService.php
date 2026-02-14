<?php

namespace App\Application\Services\Booking;

use App\Application\Booking\Actions\CancelBookingAction;
use App\Application\Booking\Actions\CreateBookingAction;
use App\Application\Contracts\Services\BookingServiceInterface;
use App\Domain\Booking\Repositories\BookingRepositoryInterface;
use App\Domain\Shared\DomainError;
use App\Domain\Shared\DomainException;
use App\Application\Booking\DTO\CreateBookingData;
use App\Domain\User\Enums\Role;
use App\Domain\Booking\Models\Booking;
use App\Domain\User\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;

class BookingService implements BookingServiceInterface
{
    public function __construct(
        private readonly BookingRepositoryInterface $bookingRepository,
        private readonly CreateBookingAction $createBookingAction,
        private readonly CancelBookingAction $cancelBookingAction,
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
        $role = $user->role instanceof Role ? $user->role->value : (string) $user->role;
        $all = $role !== Role::CUSTOMER->value;

        return $this->bookingRepository->paginateForUser($user, $all);
    }

    public function findOrFail(int $id): Booking
    {
        $booking = $this->bookingRepository->find($id);

        if ($booking === null) {
            throw new DomainException(DomainError::BOOKING_NOT_FOUND);
        }

        return $booking;
    }

    public function cancel(Booking $booking): Booking
    {
        return $this->cancelBookingAction->execute($booking);
    }
}
