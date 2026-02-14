<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Booking\BookingStoreRequest;
use App\Models\Booking;
use App\Services\Booking\BookingService;
use App\Support\Http\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    use ApiResponse;

    public function __construct(private readonly BookingService $bookingService)
    {
    }

    public function store(int $id, BookingStoreRequest $request): JsonResponse
    {
        $booking = $this->bookingService->create($request->user(), $id, $request->validated());

        return $this->created($booking, 'Booking created successfully');
    }

    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Booking::class);

        $bookings = $this->bookingService->listFor($request->user());

        return $this->success($bookings, 'OK');
    }

    public function cancel(int $id): JsonResponse
    {
        $booking = $this->bookingService->findOrFail($id);

        $this->authorize('cancel', $booking);

        $booking = $this->bookingService->cancel($booking);

        return $this->success($booking, 'Booking cancelled successfully');
    }
}
