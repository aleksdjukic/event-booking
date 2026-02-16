<?php

namespace App\Modules\Ticket\Application\Actions;

use App\Modules\Event\Application\Actions\BumpEventIndexVersionAction;
use App\Modules\Ticket\Application\DTO\UpdateTicketData;
use App\Modules\Shared\Domain\DomainError;
use App\Modules\Shared\Domain\DomainException;
use App\Modules\Ticket\Domain\Models\Ticket;
use App\Modules\Ticket\Domain\Repositories\TicketRepositoryInterface;

class UpdateTicketAction
{
    public function __construct(
        private readonly TicketRepositoryInterface $ticketRepository,
        private readonly BumpEventIndexVersionAction $bumpEventIndexVersion,
    ) {
    }

    public function execute(Ticket $ticket, UpdateTicketData $data): Ticket
    {
        $type = $data->type ?? (string) $ticket->{Ticket::COL_TYPE};

        if ($this->ticketRepository->duplicateTypeExists(
            (int) $ticket->{Ticket::COL_EVENT_ID},
            $type,
            (int) $ticket->{Ticket::COL_ID}
        )) {
            throw new DomainException(DomainError::DUPLICATE_TICKET_TYPE);
        }

        if ($data->type !== null) {
            $ticket->{Ticket::COL_TYPE} = $data->type;
        }

        if ($data->price !== null) {
            $ticket->{Ticket::COL_PRICE} = round($data->price, 2);
        }

        if ($data->quantity !== null) {
            $ticket->{Ticket::COL_QUANTITY} = $data->quantity;
        }

        $this->ticketRepository->save($ticket);
        $this->bumpEventIndexVersion->execute();

        return $ticket;
    }
}
