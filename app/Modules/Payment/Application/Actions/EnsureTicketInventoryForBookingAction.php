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
        if ($ticket->quantity <= 0) {
            throw new DomainException(DomainError::TICKET_SOLD_OUT);
        }

        if ($booking->quantity > $ticket->quantity) {
            throw new DomainException(DomainError::NOT_ENOUGH_TICKET_INVENTORY);
        }
    }
}
