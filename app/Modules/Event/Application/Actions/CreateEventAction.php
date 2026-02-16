<?php

namespace App\Modules\Event\Application\Actions;

use App\Modules\Event\Application\DTO\CreateEventData;
use App\Modules\Event\Domain\Models\Event;
use App\Modules\Event\Domain\Repositories\EventRepositoryInterface;
use App\Modules\User\Domain\Models\User;

class CreateEventAction
{
    public function __construct(private readonly EventRepositoryInterface $eventRepository)
    {
    }

    public function execute(User $user, CreateEventData $data): Event
    {
        return $this->eventRepository->create($user, $data->title, $data->description, $data->date, $data->location);
    }
}
