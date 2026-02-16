<?php

namespace App\Modules\Ticket\Application\Actions;

use App\Modules\Event\Application\Actions\BumpEventIndexVersionAction;
use App\Modules\Ticket\Application\DTO\CreateTicketData;
use App\Domain\Event\Models\Event;
use App\Modules\Shared\Domain\DomainError;
use App\Modules\Shared\Domain\DomainException;
use App\Domain\Ticket\Models\Ticket;
use App\Domain\Ticket\Repositories\TicketRepositoryInterface;

class CreateTicketAction
{
    public function __construct(
        private readonly TicketRepositoryInterface $ticketRepository,
        private readonly BumpEventIndexVersionAction $bumpEventIndexVersion,
    ) {
    }

    public function execute(Event $event, CreateTicketData $data): Ticket
    {
        if ($this->ticketRepository->duplicateTypeExists($event->id, $data->type)) {
            throw new DomainException(DomainError::DUPLICATE_TICKET_TYPE);
        }

        $ticket = $this->ticketRepository->create($event, $data->type, $data->price, $data->quantity);

        $this->bumpEventIndexVersion->execute();

        return $ticket;
    }
}
