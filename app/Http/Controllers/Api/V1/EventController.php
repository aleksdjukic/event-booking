<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Event\EventIndexRequest;
use App\Http\Requests\Api\V1\Event\EventStoreRequest;
use App\Http\Requests\Api\V1\Event\EventUpdateRequest;
use App\Models\Event;
use App\Services\Event\EventService;
use App\Support\Http\ApiResponse;
use Illuminate\Http\JsonResponse;

class EventController extends Controller
{
    use ApiResponse;

    public function __construct(private readonly EventService $eventService)
    {
    }

    public function index(EventIndexRequest $request): JsonResponse
    {
        $events = $this->eventService->index($request->validated());

        return $this->success($events, 'OK');
    }

    public function show(int $id): JsonResponse
    {
        $event = $this->eventService->show($id);

        return $this->success($event, 'OK');
    }

    public function store(EventStoreRequest $request): JsonResponse
    {
        $this->authorize('create', Event::class);

        $event = $this->eventService->create($request->user(), $request->validated());

        return $this->created($event, 'Event created successfully');
    }

    public function update(EventUpdateRequest $request, int $id): JsonResponse
    {
        $event = $this->eventService->findOrFail($id);

        $this->authorize('update', $event);

        $event = $this->eventService->update($event, $request->validated());

        return $this->success($event, 'Event updated successfully');
    }

    public function destroy(int $id): JsonResponse
    {
        $event = $this->eventService->findOrFail($id);

        $this->authorize('delete', $event);

        $this->eventService->delete($event);

        return $this->success(null, 'Event deleted successfully');
    }
}
