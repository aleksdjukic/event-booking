<?php

namespace App\Infrastructure\Persistence\Eloquent;

use App\Domain\Event\Repositories\EventRepositoryInterface;
use App\Domain\Event\Models\Event;
use App\Domain\User\Models\User;
use App\Support\Traits\CommonQueryScopes;
use Illuminate\Pagination\LengthAwarePaginator;

class EventRepository implements EventRepositoryInterface
{
    use CommonQueryScopes;

    /**
     * @return LengthAwarePaginator<int, Event>
     */
    public function paginate(int $page, ?string $date, ?string $search, ?string $location): LengthAwarePaginator
    {
        $eventQuery = Event::query();
        $this->searchByTitle($eventQuery, $search, Event::COL_TITLE);
        $this->filterByDate($eventQuery, $date, Event::COL_DATE);

        if ($location !== null && $location !== '') {
            $eventQuery->where(Event::COL_LOCATION, 'like', '%'.$location.'%');
        }

        return $eventQuery->paginate(page: $page);
    }

    public function find(int $id): ?Event
    {
        return Event::query()->find($id);
    }

    public function findWithTickets(int $id): ?Event
    {
        return Event::query()->with(Event::REL_TICKETS)->find($id);
    }

    public function create(User $user, string $title, ?string $description, string $date, string $location): Event
    {
        $event = new Event();
        $event->{Event::COL_TITLE} = $title;
        $event->{Event::COL_DESCRIPTION} = $description;
        $event->{Event::COL_DATE} = $date;
        $event->{Event::COL_LOCATION} = $location;
        $event->{Event::COL_CREATED_BY} = $user->id;
        $event->save();

        return $event;
    }

    public function update(Event $event, string $title, ?string $description, string $date, string $location): Event
    {
        $event->{Event::COL_TITLE} = $title;
        $event->{Event::COL_DESCRIPTION} = $description;
        $event->{Event::COL_DATE} = $date;
        $event->{Event::COL_LOCATION} = $location;
        $event->save();

        return $event;
    }

    public function delete(Event $event): void
    {
        $event->delete();
    }
}
