<?php

namespace App\Services\Ticket;

use App\Models\Event;
use App\Models\Ticket;
use App\Services\Support\ServiceException;
use Illuminate\Support\Facades\Cache;

class TicketService
{
    public function findEvent(int $eventId): ?Event
    {
        return Event::query()->find($eventId);
    }

    public function findTicket(int $id): ?Ticket
    {
        return Ticket::query()->find($id);
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
            throw new ServiceException('Ticket type already exists for this event.', 409);
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
            throw new ServiceException('Ticket type already exists for this event.', 409);
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
