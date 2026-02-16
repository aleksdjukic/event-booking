<?php

namespace App\Modules\Event\Application\Contracts;

use App\Modules\Event\Application\DTO\CreateEventData;
use App\Modules\Event\Application\DTO\ListEventsData;
use App\Modules\Event\Application\DTO\UpdateEventData;
use App\Modules\Event\Domain\Models\Event;
use App\Modules\User\Domain\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;

interface EventServiceInterface
{
    /**
     * @return LengthAwarePaginator<int, Event>
     */
    public function index(ListEventsData $query): LengthAwarePaginator;

    public function show(int $id): Event;

    public function create(User $user, CreateEventData $data): Event;

    public function update(Event $event, UpdateEventData $data): Event;

    public function delete(Event $event): void;
}
