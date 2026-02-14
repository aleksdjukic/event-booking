<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Ticket\TicketStoreRequest;
use App\Http\Requests\Api\V1\Ticket\TicketUpdateRequest;
use App\Models\Ticket;
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
        $event = $this->ticketService->findEventOrFail($event_id);

        $this->authorize('create', [Ticket::class, $event]);

        $ticket = $this->ticketService->create($event, $request->validated());

        return $this->created($ticket, 'Ticket created successfully');
    }

    public function update(TicketUpdateRequest $request, int $id): JsonResponse
    {
        $ticket = $this->ticketService->findTicketOrFail($id);

        $this->authorize('update', $ticket);

        $ticket = $this->ticketService->update($ticket, $request->validated());

        return $this->success($ticket, 'Ticket updated successfully');
    }

    public function destroy(int $id): JsonResponse
    {
        $ticket = $this->ticketService->findTicketOrFail($id);

        $this->authorize('delete', $ticket);

        $this->ticketService->delete($ticket);

        return $this->success(null, 'Ticket deleted successfully');
    }
}
