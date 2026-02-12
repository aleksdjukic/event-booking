<?php

namespace App\Http\Middleware;

use App\Models\Booking;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PreventDoubleBooking
{
    /**
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $ticketId = (int) $request->route('id');
        $user = $request->user();

        $hasActiveBooking = Booking::query()
            ->where('user_id', $user->id)
            ->where('ticket_id', $ticketId)
            ->whereIn('status', ['pending', 'confirmed'])
            ->exists();

        if ($hasActiveBooking) {
            return response()->json([
                'success' => false,
                'message' => 'You already have an active booking for this ticket.',
                'data' => null,
                'errors' => null,
            ], 409);
        }

        return $next($request);
    }
}
