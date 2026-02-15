<?php

namespace App\Infrastructure\Persistence\Eloquent;

use App\Domain\Ticket\Repositories\TicketRepositoryInterface;
use App\Domain\Event\Models\Event;
use App\Domain\Ticket\Models\Ticket;

class TicketRepository implements TicketRepositoryInterface
{
    public function find(int $id): ?Ticket
    {
        return Ticket::query()->find($id);
    }

    public function findForUpdate(int $id): ?Ticket
    {
        return Ticket::query()->whereKey($id)->lockForUpdate()->first();
    }

    public function findForUpdateWithEvent(int $id): ?Ticket
    {
        return Ticket::query()
            ->with(Ticket::REL_EVENT)
            ->whereKey($id)
            ->lockForUpdate()
            ->first();
    }

    public function duplicateTypeExists(int $eventId, string $type, ?int $excludeTicketId = null): bool
    {
        $query = Ticket::query()
            ->where(Ticket::COL_EVENT_ID, $eventId)
            ->where(Ticket::COL_TYPE, $type);

        if ($excludeTicketId !== null) {
            $query->where(Ticket::COL_ID, '!=', $excludeTicketId);
        }

        return $query->exists();
    }

    public function create(Event $event, string $type, float $price, int $quantity): Ticket
    {
        $ticket = new Ticket();
        $ticket->{Ticket::COL_EVENT_ID} = $event->id;
        $ticket->{Ticket::COL_TYPE} = $type;
        $ticket->{Ticket::COL_PRICE} = round($price, 2);
        $ticket->{Ticket::COL_QUANTITY} = $quantity;
        $ticket->save();

        return $ticket;
    }

    public function save(Ticket $ticket): Ticket
    {
        $ticket->save();

        return $ticket;
    }

    public function delete(Ticket $ticket): void
    {
        $ticket->delete();
    }
}
