<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Ticket\TicketStoreRequest;
use App\Http\Requests\Api\V1\Ticket\TicketUpdateRequest;
use App\Models\Ticket;
use App\Services\Support\ServiceException;
use App\Services\Ticket\TicketService;
use App\Support\Http\ApiResponse;
use Illuminate\Http\JsonResponse;

class TicketController extends Controller
{
    use ApiResponse;

    public function __construct(private readonly TicketService $ticketService)
    {
    }

    public function store(TicketStoreRequest $request, int $event_id): JsonResponse
    {
        $event = $this->ticketService->findEvent($event_id);

        if ($event === null) {
            return $this->error('Event not found.', 404);
        }

        $this->authorize('create', [Ticket::class, $event]);

        try {
            $ticket = $this->ticketService->create($event, $request->validated());
        } catch (ServiceException $exception) {
            return $this->error($exception->getMessage(), $exception->status());
        }

        return $this->created($ticket, 'Ticket created successfully');
    }

    public function update(TicketUpdateRequest $request, int $id): JsonResponse
    {
        $ticket = $this->ticketService->findTicket($id);

        if ($ticket === null) {
            return $this->error('Ticket not found.', 404);
        }

        $this->authorize('update', $ticket);

        try {
            $ticket = $this->ticketService->update($ticket, $request->validated());
        } catch (ServiceException $exception) {
            return $this->error($exception->getMessage(), $exception->status());
        }

        return $this->success($ticket, 'Ticket updated successfully');
    }

    public function destroy(int $id): JsonResponse
    {
        $ticket = $this->ticketService->findTicket($id);

        if ($ticket === null) {
            return $this->error('Ticket not found.', 404);
        }

        $this->authorize('delete', $ticket);

        $this->ticketService->delete($ticket);

        return $this->success(null, 'Ticket deleted successfully');
    }
}
