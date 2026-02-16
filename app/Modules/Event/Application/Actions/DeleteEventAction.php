<?php

namespace App\Modules\Event\Application\Actions;

use App\Modules\Event\Domain\Models\Event;
use App\Modules\Event\Domain\Repositories\EventRepositoryInterface;

class DeleteEventAction
{
    public function __construct(private readonly EventRepositoryInterface $eventRepository)
    {
    }

    public function execute(Event $event): void
    {
        $this->eventRepository->delete($event);
    }
}
