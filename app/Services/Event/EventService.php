<?php

namespace App\Services\Event;

use App\Models\Event;
use App\Models\User;
use App\Support\Traits\CommonQueryScopes;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;

class EventService
{
    use CommonQueryScopes;

    /**
     * @param  array<string, mixed>  $query
     */
    public function index(array $query): LengthAwarePaginator
    {
        $page = max(1, (int) ($query['page'] ?? 1));
        $queryKeys = array_keys($query);
        $nonCacheableKeys = array_diff($queryKeys, ['page']);

        if ($nonCacheableKeys === []) {
            $version = Cache::get('events:index:version', 1);
            $cacheKey = 'events:index:v'.$version.':page:'.$page;

            return Cache::remember($cacheKey, 120, fn () => Event::query()->paginate());
        }

        $eventQuery = Event::query();
        $this->searchByTitle($eventQuery, isset($query['search']) ? (string) $query['search'] : null);
        $this->filterByDate($eventQuery, isset($query['date']) ? (string) $query['date'] : null);

        if (isset($query['location']) && $query['location'] !== '') {
            $eventQuery->where('location', 'like', '%'.(string) $query['location'].'%');
        }

        return $eventQuery->paginate();
    }

    public function show(int $id): ?Event
    {
        return Event::query()->with('tickets')->find($id);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(User $user, array $data): Event
    {
        $event = new Event();
        $event->title = (string) $data['title'];
        $event->description = isset($data['description']) ? (string) $data['description'] : null;
        $event->date = (string) $data['date'];
        $event->location = (string) $data['location'];
        $event->created_by = $user->id;
        $event->save();

        return $event;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(Event $event, array $data): Event
    {
        $event->title = (string) $data['title'];
        $event->description = isset($data['description']) ? (string) $data['description'] : null;
        $event->date = (string) $data['date'];
        $event->location = (string) $data['location'];
        $event->save();

        return $event;
    }

    public function delete(Event $event): void
    {
        $event->delete();
    }
}
