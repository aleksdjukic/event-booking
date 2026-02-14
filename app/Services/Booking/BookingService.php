<?php

namespace App\Services\Booking;

use App\Enums\Role;
use App\Models\Booking;
use App\Models\Ticket;
use App\Models\User;
use App\Services\Support\ServiceException;
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
            throw new ServiceException('Ticket not found.', 404);
        }

        if ($ticket->quantity <= 0) {
            throw new ServiceException('Ticket is sold out.', 409);
        }

        $quantity = (int) $data['quantity'];
        if ($quantity > $ticket->quantity) {
            throw new ServiceException('Not enough ticket inventory.', 409);
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

    public function find(int $id): ?Booking
    {
        return Booking::query()->find($id);
    }

    public function cancel(Booking $booking): Booking
    {
        if ($booking->status !== 'pending') {
            throw new ServiceException('Only pending bookings can be cancelled.', 409);
        }

        $booking->status = 'cancelled';
        $booking->save();

        return $booking;
    }
}
