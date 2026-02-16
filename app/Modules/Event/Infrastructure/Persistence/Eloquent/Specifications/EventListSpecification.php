<?php

namespace App\Modules\Event\Infrastructure\Persistence\Eloquent\Specifications;

use App\Modules\Event\Domain\Models\Event;
use App\Modules\Event\Domain\Queries\EventListQuery;
use App\Modules\Shared\Support\Traits\CommonQueryScopes;
use Illuminate\Database\Eloquent\Builder;

class EventListSpecification
{
    use CommonQueryScopes;

    /**
     * @param Builder<Event> $query
     */
    public function apply(Builder $query, EventListQuery $filters): void
    {
        $this->searchByTitle($query, $filters->search, Event::COL_TITLE);
        $this->filterByDate($query, $filters->date, Event::COL_DATE);

        if ($filters->location !== null && $filters->location !== '') {
            $query->where(Event::COL_LOCATION, 'like', '%'.$filters->location.'%');
        }
    }
}
