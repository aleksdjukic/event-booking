<?php

namespace App\Modules\Ticket\Domain\Repositories;

use App\Modules\Event\Domain\Models\Event;
use App\Modules\Ticket\Domain\Models\Ticket;

interface TicketRepositoryInterface
{
    public function find(int $id): ?Ticket;

    public function findForUpdate(int $id): ?Ticket;

    public function findForUpdateWithEvent(int $id): ?Ticket;

    public function duplicateTypeExists(int $eventId, string $type, ?int $excludeTicketId = null): bool;

    public function create(Event $event, string $type, float $price, int $quantity): Ticket;

    public function save(Ticket $ticket): Ticket;

    public function delete(Ticket $ticket): void;
}
