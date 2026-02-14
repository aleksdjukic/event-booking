<?php

namespace App\Application\Event\Actions;

use App\Application\Event\DTO\CreateEventData;
use App\Domain\Event\Models\Event;
use App\Domain\Event\Repositories\EventRepositoryInterface;
use App\Domain\User\Models\User;

class CreateEventAction
{
    public function __construct(private readonly EventRepositoryInterface $eventRepository)
    {
    }

    public function execute(User $user, CreateEventData $data): Event
    {
        return $this->eventRepository->create($user, $data);
    }
}
