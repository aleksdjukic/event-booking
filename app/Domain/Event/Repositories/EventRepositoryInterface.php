<?php

namespace App\Domain\Event\Repositories;

use App\Domain\Event\Models\Event;
use App\Domain\User\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;

interface EventRepositoryInterface
{
    /**
     * @return LengthAwarePaginator<int, Event>
     */
    public function paginate(int $page, ?string $date, ?string $search, ?string $location): LengthAwarePaginator;

    public function find(int $id): ?Event;

    public function findWithTickets(int $id): ?Event;

    public function create(User $user, string $title, ?string $description, string $date, string $location): Event;

    public function update(Event $event, string $title, ?string $description, string $date, string $location): Event;

    public function delete(Event $event): void;
}
