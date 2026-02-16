<?php

namespace App\Modules\Event\Presentation\Http\Controllers;

use App\Application\Contracts\Services\EventServiceInterface;
use App\Http\Controllers\Api\V1\ApiController;
use App\Http\Requests\Api\V1\Event\ListEventsRequest;
use App\Http\Requests\Api\V1\Event\CreateEventRequest;
use App\Http\Requests\Api\V1\Event\DeleteEventRequest;
use App\Http\Requests\Api\V1\Event\UpdateEventRequest;
use App\Http\Resources\Api\V1\Event\EventResource;
use App\Domain\Event\Models\Event;
use Illuminate\Http\JsonResponse;

class EventController extends ApiController
{
    public function __construct(private readonly EventServiceInterface $eventService)
    {
    }

    public function index(ListEventsRequest $request): JsonResponse
    {
        $events = $this->eventService->index($request->toDto());

        return $this->success(EventResource::collection($events), 'OK');
    }

    public function show(Event $event): JsonResponse
    {
        $event = $this->eventService->show($event->id);

        return $this->success(EventResource::make($event), 'OK');
    }

    public function store(CreateEventRequest $request): JsonResponse
    {
        $event = $this->eventService->create(
            $request->user(),
            $request->toDto()
        );

        return $this->created(EventResource::make($event), 'Event created successfully');
    }

    public function update(UpdateEventRequest $request, Event $event): JsonResponse
    {
        $event = $this->eventService->update($event, $request->toDto());

        return $this->success(EventResource::make($event), 'Event updated successfully');
    }

    public function destroy(DeleteEventRequest $request, Event $event): JsonResponse
    {
        $this->eventService->delete($event);

        return $this->success(null, 'Event deleted successfully');
    }
}
