<?php

namespace App\Http\Middleware;

use App\Domain\Booking\Enums\BookingStatus;
use App\Domain\Booking\Models\Booking;
use App\Domain\Ticket\Models\Ticket;
use App\Support\Http\ApiResponder;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PreventDoubleBooking
{
    public function __construct(private readonly ApiResponder $responder)
    {
    }

    /**
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $ticketParam = $request->route('ticket');
        $ticketId = is_object($ticketParam) ? (int) $ticketParam->{Ticket::COL_ID} : (int) $ticketParam;
        $user = $request->user();

        $hasActiveBooking = Booking::query()
            ->where(Booking::COL_USER_ID, $user->id)
            ->where(Booking::COL_TICKET_ID, $ticketId)
            ->whereIn(Booking::COL_STATUS, [
                BookingStatus::PENDING->value,
                BookingStatus::CONFIRMED->value,
            ])
            ->exists();

        if ($hasActiveBooking) {
            return $this->responder->error('You already have an active booking for this ticket.', 409);
        }

        return $next($request);
    }
}
