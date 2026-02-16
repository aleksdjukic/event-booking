<?php

namespace App\Modules\Event\Application\Actions;

use App\Modules\Event\Application\DTO\ListEventsData;
use App\Modules\Event\Domain\Models\Event;
use App\Modules\Event\Domain\Queries\EventListQuery;
use App\Modules\Event\Domain\Repositories\EventRepositoryInterface;
use App\Modules\Event\Domain\Support\EventCache;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;

class ListEventsAction
{
    public function __construct(private readonly EventRepositoryInterface $eventRepository)
    {
    }

    /**
     * @return LengthAwarePaginator<int, Event>
     */
    public function execute(ListEventsData $query): LengthAwarePaginator
    {
        $listQuery = new EventListQuery(
            page: $query->page,
            date: $query->date,
            search: $query->search,
            location: $query->location,
        );

        if ($listQuery->hasOnlyPageFilter()) {
            $version = (int) Cache::get(EventCache::INDEX_VERSION_KEY, 1);
            $cacheKey = EventCache::indexPageKey($version, $listQuery->page);

            return Cache::remember(
                $cacheKey,
                EventCache::INDEX_TTL_SECONDS,
                fn () => $this->eventRepository->paginate($listQuery)
            );
        }

        return $this->eventRepository->paginate($listQuery);
    }
}
