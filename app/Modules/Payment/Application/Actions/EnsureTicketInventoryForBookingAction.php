<?php

namespace App\Modules\Payment\Application\Actions;

use App\Modules\Booking\Domain\Models\Booking;
use App\Modules\Shared\Domain\DomainError;
use App\Modules\Shared\Domain\DomainException;
use App\Modules\Ticket\Domain\Models\Ticket;

class EnsureTicketInventoryForBookingAction
{
    public function execute(Booking $booking, Ticket $ticket): void
    {
        if ($ticket->{Ticket::COL_QUANTITY} <= 0) {
            throw new DomainException(DomainError::TICKET_SOLD_OUT);
        }

        if ($booking->{Booking::COL_QUANTITY} > $ticket->{Ticket::COL_QUANTITY}) {
            throw new DomainException(DomainError::NOT_ENOUGH_TICKET_INVENTORY);
        }
    }
}
