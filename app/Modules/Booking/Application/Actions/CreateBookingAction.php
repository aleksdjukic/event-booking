<?php

namespace App\Modules\Booking\Application\Actions;

use App\Modules\Booking\Domain\Repositories\BookingRepositoryInterface;
use App\Modules\Shared\Domain\DomainError;
use App\Modules\Shared\Domain\DomainException;
use App\Modules\Ticket\Domain\Repositories\TicketRepositoryInterface;
use App\Modules\Ticket\Domain\Models\Ticket;
use App\Modules\Booking\Application\DTO\CreateBookingData;
use App\Modules\Booking\Domain\Enums\BookingStatus;
use App\Modules\Booking\Domain\Models\Booking;
use App\Modules\User\Domain\Models\User;
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

                if ($ticket->{Ticket::COL_QUANTITY} <= 0) {
                    throw new DomainException(DomainError::TICKET_SOLD_OUT);
                }

                if ($data->quantity > $ticket->{Ticket::COL_QUANTITY}) {
                    throw new DomainException(DomainError::NOT_ENOUGH_TICKET_INVENTORY);
                }

                return $this->bookingRepository->create(
                    $user,
                    (int) $ticket->{Ticket::COL_ID},
                    $data->quantity,
                    BookingStatus::PENDING
                );
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
