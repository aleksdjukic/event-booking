<?php

namespace App\Application\Booking\Actions;

use App\Domain\Booking\Repositories\BookingRepositoryInterface;
use App\Domain\Shared\DomainError;
use App\Domain\Shared\DomainException;
use App\Domain\Ticket\Repositories\TicketRepositoryInterface;
use App\Application\Booking\DTO\CreateBookingData;
use App\Domain\Booking\Enums\BookingStatus;
use App\Domain\Booking\Models\Booking;
use App\Domain\User\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

class CreateBookingAction
{
    public function __construct(
        private readonly BookingRepositoryInterface $bookingRepository,
        private readonly TicketRepositoryInterface $ticketRepository,
    ) {
    }

    public function execute(User $user, int $ticketId, CreateBookingData $data): Booking
    {
        try {
            return DB::transaction(function () use ($user, $ticketId, $data): Booking {
                $ticket = $this->ticketRepository->findForUpdate($ticketId);

                if ($ticket === null) {
                    throw new DomainException(DomainError::TICKET_NOT_FOUND);
                }

                if ($ticket->quantity <= 0) {
                    throw new DomainException(DomainError::TICKET_SOLD_OUT);
                }

                if ($data->quantity > $ticket->quantity) {
                    throw new DomainException(DomainError::NOT_ENOUGH_TICKET_INVENTORY);
                }

                return $this->bookingRepository->create($user, (int) $ticket->id, $data->quantity, BookingStatus::PENDING);
            });
        } catch (QueryException $exception) {
            if ($this->isActiveBookingUniqueConstraintViolation($exception)) {
                throw new DomainException(DomainError::ACTIVE_BOOKING_ALREADY_EXISTS);
            }

            throw $exception;
        }
    }

    private function isActiveBookingUniqueConstraintViolation(QueryException $exception): bool
    {
        $message = strtolower($exception->getMessage());

        return str_contains($message, 'bookings_active_booking_key_unique')
            || str_contains($message, Booking::COL_ACTIVE_BOOKING_KEY);
    }
}
