<?php

namespace App\Modules\Event\Application\Actions;

use App\Modules\Event\Domain\Models\Event;
use App\Modules\Event\Domain\Repositories\EventRepositoryInterface;
use App\Modules\Shared\Domain\DomainError;
use App\Modules\Shared\Domain\DomainException;

class FindEventWithTicketsAction
{
    public function __construct(private readonly EventRepositoryInterface $eventRepository)
    {
    }

    public function execute(int $id): Event
    {
        $event = $this->eventRepository->findWithTickets($id);

        if ($event === null) {
            throw new DomainException(DomainError::EVENT_NOT_FOUND);
        }

        return $event;
    }
}
