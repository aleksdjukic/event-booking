<?php

namespace App\Services\Booking;

use App\Domain\Shared\DomainError;
use App\Domain\Shared\DomainException;
use App\Enums\Role;
use App\Models\Booking;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;

class BookingService
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function create(User $user, int $ticketId, array $data): Booking
    {
        $ticket = Ticket::query()->find($ticketId);

        if ($ticket === null) {
            throw new DomainException(DomainError::TICKET_NOT_FOUND);
        }

        if ($ticket->quantity <= 0) {
            throw new DomainException(DomainError::TICKET_SOLD_OUT);
        }

        $quantity = (int) $data['quantity'];
        if ($quantity > $ticket->quantity) {
            throw new DomainException(DomainError::NOT_ENOUGH_TICKET_INVENTORY);
        }

        $booking = new Booking();
        $booking->user_id = $user->id;
        $booking->ticket_id = $ticket->id;
        $booking->quantity = $quantity;
        $booking->status = 'pending';
        $booking->save();

        return $booking;
    }

    public function listFor(User $user): LengthAwarePaginator
    {
        $query = Booking::query()->with(['ticket', 'payment']);
        $role = $user->role instanceof Role ? $user->role->value : (string) $user->role;

        if ($role === Role::CUSTOMER->value) {
            $query->where('user_id', $user->id);
        }

        return $query->paginate();
    }

    public function findOrFail(int $id): Booking
    {
        $booking = Booking::query()->find($id);

        if ($booking === null) {
            throw new DomainException(DomainError::BOOKING_NOT_FOUND);
        }

        return $booking;
    }

    public function cancel(Booking $booking): Booking
    {
        if ($booking->status !== 'pending') {
            throw new DomainException(DomainError::BOOKING_NOT_PENDING);
        }

        $booking->status = 'cancelled';
        $booking->save();

        return $booking;
    }
}
