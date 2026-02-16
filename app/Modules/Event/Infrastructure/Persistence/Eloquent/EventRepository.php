<?php

namespace App\Modules\Event\Infrastructure\Persistence\Eloquent;

use App\Modules\Event\Domain\Repositories\EventRepositoryInterface;
use App\Modules\Event\Domain\Models\Event;
use App\Modules\Event\Domain\Queries\EventListQuery;
use App\Modules\Event\Infrastructure\Persistence\Eloquent\Specifications\EventListSpecification;
use App\Modules\User\Domain\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;

class EventRepository implements EventRepositoryInterface
{
    public function __construct(private readonly EventListSpecification $listSpecification)
    {
    }

    /**
     * @return LengthAwarePaginator<int, Event>
     */
    public function paginate(EventListQuery $query): LengthAwarePaginator
    {
        $eventQuery = Event::query();
        $this->listSpecification->apply($eventQuery, $query);

        return $eventQuery->paginate(page: $query->page);
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
        $event->{Event::COL_CREATED_BY} = $user->{User::COL_ID};
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
