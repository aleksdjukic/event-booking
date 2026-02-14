<?php

namespace App\Application\Services\Ticket;

use App\Application\Contracts\Services\TicketServiceInterface;
use App\Application\Ticket\Actions\CreateTicketAction;
use App\Application\Ticket\Actions\DeleteTicketAction;
use App\Application\Ticket\Actions\UpdateTicketAction;
use App\Application\Ticket\DTO\CreateTicketData;
use App\Application\Ticket\DTO\UpdateTicketData;
use App\Domain\Shared\DomainError;
use App\Domain\Shared\DomainException;
use App\Domain\Ticket\Repositories\TicketRepositoryInterface;
use App\Domain\Event\Models\Event;
use App\Domain\Ticket\Models\Ticket;

class TicketService implements TicketServiceInterface
{
    public function __construct(
        private readonly TicketRepositoryInterface $ticketRepository,
        private readonly CreateTicketAction $createTicketAction,
        private readonly UpdateTicketAction $updateTicketAction,
        private readonly DeleteTicketAction $deleteTicketAction,
    ) {
    }

    public function findEventOrFail(int $eventId): Event
    {
        $event = Event::query()->find($eventId);

        if ($event === null) {
            throw new DomainException(DomainError::EVENT_NOT_FOUND);
        }

        return $event;
    }

    public function findTicketOrFail(int $id): Ticket
    {
        $ticket = $this->ticketRepository->find($id);

        if ($ticket === null) {
            throw new DomainException(DomainError::TICKET_NOT_FOUND);
        }

        return $ticket;
    }

    public function create(Event $event, CreateTicketData $data): Ticket
    {
        return $this->createTicketAction->execute($event, $data);
    }

    public function update(Ticket $ticket, UpdateTicketData $data): Ticket
    {
        return $this->updateTicketAction->execute($ticket, $data);
    }

    public function delete(Ticket $ticket): void
    {
        $this->deleteTicketAction->execute($ticket);
    }
}
