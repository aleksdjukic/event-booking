<?php

namespace App\Services\Ticket;

use App\Domain\Shared\DomainError;
use App\Domain\Shared\DomainException;
use App\Models\Event;
use App\Models\Ticket;
use Illuminate\Support\Facades\Cache;

class TicketService
{
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
        $ticket = Ticket::query()->find($id);

        if ($ticket === null) {
            throw new DomainException(DomainError::TICKET_NOT_FOUND);
        }

        return $ticket;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(Event $event, array $data): Ticket
    {
        $duplicateTypeExists = Ticket::query()
            ->where('event_id', $event->id)
            ->where('type', (string) $data['type'])
            ->exists();

        if ($duplicateTypeExists) {
            throw new DomainException(DomainError::DUPLICATE_TICKET_TYPE);
        }

        $ticket = new Ticket();
        $ticket->event_id = $event->id;
        $ticket->type = (string) $data['type'];
        $ticket->price = number_format((float) $data['price'], 2, '.', '');
        $ticket->quantity = (int) $data['quantity'];
        $ticket->save();

        $this->bumpEventIndexVersion();

        return $ticket;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(Ticket $ticket, array $data): Ticket
    {
        $type = isset($data['type']) ? (string) $data['type'] : $ticket->type;
        $duplicateTypeExists = Ticket::query()
            ->where('event_id', $ticket->event_id)
            ->where('type', $type)
            ->where('id', '!=', $ticket->id)
            ->exists();

        if ($duplicateTypeExists) {
            throw new DomainException(DomainError::DUPLICATE_TICKET_TYPE);
        }

        if (array_key_exists('type', $data)) {
            $ticket->type = (string) $data['type'];
        }

        if (array_key_exists('price', $data)) {
            $ticket->price = number_format((float) $data['price'], 2, '.', '');
        }

        if (array_key_exists('quantity', $data)) {
            $ticket->quantity = (int) $data['quantity'];
        }

        $ticket->save();
        $this->bumpEventIndexVersion();

        return $ticket;
    }

    public function delete(Ticket $ticket): void
    {
        $ticket->delete();
        $this->bumpEventIndexVersion();
    }

    private function bumpEventIndexVersion(): void
    {
        Cache::add('events:index:version', 1);
        Cache::increment('events:index:version');
    }
}
