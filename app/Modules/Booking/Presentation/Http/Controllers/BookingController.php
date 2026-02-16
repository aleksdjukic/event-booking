<?php

namespace App\Modules\Booking\Presentation\Http\Controllers;

use App\Modules\Booking\Application\Contracts\BookingServiceInterface;
use App\Modules\Shared\Presentation\Http\Controllers\ApiController;
use App\Domain\Booking\Models\Booking;
use App\Domain\Ticket\Models\Ticket;
use App\Modules\Booking\Presentation\Http\Requests\CancelBookingRequest;
use App\Modules\Booking\Presentation\Http\Requests\CreateBookingRequest;
use App\Modules\Booking\Presentation\Http\Requests\ListBookingsRequest;
use App\Modules\Booking\Presentation\Http\Resources\BookingResource;
use Illuminate\Http\JsonResponse;

class BookingController extends ApiController
{
    public function __construct(private readonly BookingServiceInterface $bookingService)
    {
    }

    public function store(Ticket $ticket, CreateBookingRequest $request): JsonResponse
    {
        $booking = $this->bookingService->create(
            $request->user(),
            $ticket->id,
            $request->toDto()
        );

        return $this->created(BookingResource::make($booking), 'Booking created successfully');
    }

    public function index(ListBookingsRequest $request): JsonResponse
    {
        $bookings = $this->bookingService->listFor($request->user());

        return $this->success(BookingResource::collection($bookings), 'OK');
    }

    public function cancel(CancelBookingRequest $request, Booking $booking): JsonResponse
    {
        $booking = $this->bookingService->cancel($booking);

        return $this->success(BookingResource::make($booking), 'Booking cancelled successfully');
    }
}
