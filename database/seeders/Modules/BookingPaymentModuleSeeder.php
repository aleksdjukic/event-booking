<?php

namespace Database\Seeders\Modules;

use App\Modules\Booking\Domain\Enums\BookingStatus;
use App\Modules\Booking\Domain\Models\Booking;
use App\Modules\Payment\Domain\Enums\PaymentStatus;
use App\Modules\Payment\Domain\Models\Payment;
use App\Modules\Ticket\Domain\Models\Ticket;
use App\Modules\User\Domain\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;

class BookingPaymentModuleSeeder extends Seeder
{
    public function run(): void
    {
        $customers = User::query()
            ->where('email', 'like', 'customer%@example.com')
            ->orderBy('email')
            ->limit(10)
            ->get();

        $tickets = Ticket::query()->orderBy('id')->get();

        Payment::query()->delete();
        Booking::query()->delete();
        Ticket::query()->update(['quantity' => 50]);

        $bookings = $this->seedBookings($customers, $tickets);
        $this->seedPayments($bookings, $tickets);
        $this->syncRemainingTicketQuantities();
    }

    /**
     * @param Collection<int, User> $customers
     * @param Collection<int, Ticket> $tickets
     * @return Collection<int, Booking>
     */
    private function seedBookings(Collection $customers, Collection $tickets): Collection
    {
        $bookingsPlan = [
            ['ticket_index' => 0, 'customer_index' => 0, 'quantity' => 2, 'status' => BookingStatus::CONFIRMED],
            ['ticket_index' => 1, 'customer_index' => 1, 'quantity' => 1, 'status' => BookingStatus::PENDING],
            ['ticket_index' => 2, 'customer_index' => 2, 'quantity' => 3, 'status' => BookingStatus::CANCELLED],
            ['ticket_index' => 3, 'customer_index' => 3, 'quantity' => 2, 'status' => BookingStatus::CONFIRMED],
            ['ticket_index' => 4, 'customer_index' => 4, 'quantity' => 5, 'status' => BookingStatus::PENDING],
            ['ticket_index' => 5, 'customer_index' => 5, 'quantity' => 1, 'status' => BookingStatus::CONFIRMED],
            ['ticket_index' => 6, 'customer_index' => 6, 'quantity' => 4, 'status' => BookingStatus::CANCELLED],
            ['ticket_index' => 7, 'customer_index' => 7, 'quantity' => 2, 'status' => BookingStatus::PENDING],
            ['ticket_index' => 8, 'customer_index' => 8, 'quantity' => 3, 'status' => BookingStatus::CONFIRMED],
            ['ticket_index' => 9, 'customer_index' => 9, 'quantity' => 1, 'status' => BookingStatus::PENDING],
            ['ticket_index' => 10, 'customer_index' => 0, 'quantity' => 2, 'status' => BookingStatus::CONFIRMED],
            ['ticket_index' => 11, 'customer_index' => 1, 'quantity' => 4, 'status' => BookingStatus::PENDING],
            ['ticket_index' => 12, 'customer_index' => 2, 'quantity' => 1, 'status' => BookingStatus::CANCELLED],
            ['ticket_index' => 13, 'customer_index' => 3, 'quantity' => 3, 'status' => BookingStatus::CONFIRMED],
            ['ticket_index' => 14, 'customer_index' => 4, 'quantity' => 2, 'status' => BookingStatus::PENDING],
            ['ticket_index' => 0, 'customer_index' => 5, 'quantity' => 1, 'status' => BookingStatus::PENDING],
            ['ticket_index' => 4, 'customer_index' => 6, 'quantity' => 2, 'status' => BookingStatus::CONFIRMED],
            ['ticket_index' => 8, 'customer_index' => 7, 'quantity' => 1, 'status' => BookingStatus::PENDING],
            ['ticket_index' => 10, 'customer_index' => 8, 'quantity' => 2, 'status' => BookingStatus::CONFIRMED],
            ['ticket_index' => 14, 'customer_index' => 9, 'quantity' => 3, 'status' => BookingStatus::CANCELLED],
        ];

        $bookings = collect();

        foreach ($bookingsPlan as $plan) {
            $bookings->push(Booking::query()->create([
                'user_id' => $customers[$plan['customer_index']]->id,
                'ticket_id' => $tickets[$plan['ticket_index']]->id,
                'quantity' => $plan['quantity'],
                'status' => $plan['status']->value,
            ]));
        }

        return $bookings;
    }

    /**
     * @param Collection<int, Booking> $bookings
     * @param Collection<int, Ticket> $tickets
     */
    private function seedPayments(Collection $bookings, Collection $tickets): void
    {
        $confirmedBookings = $bookings
            ->filter(fn (Booking $booking): bool => $booking->status === BookingStatus::CONFIRMED)
            ->values();

        foreach ($confirmedBookings as $booking) {
            $ticket = $tickets->firstWhere('id', $booking->ticket_id);
            if (! $ticket instanceof Ticket) {
                continue;
            }

            Payment::query()->create([
                'booking_id' => $booking->id,
                'amount' => number_format($booking->quantity * (float) $ticket->price, 2, '.', ''),
                'status' => PaymentStatus::SUCCESS->value,
            ]);
        }

        $cancelledBooking = $bookings->first(
            fn (Booking $booking): bool => $booking->status === BookingStatus::CANCELLED
        );

        if (! $cancelledBooking instanceof Booking) {
            return;
        }

        $cancelledTicket = $tickets->firstWhere('id', $cancelledBooking->ticket_id);
        if (! $cancelledTicket instanceof Ticket) {
            return;
        }

        Payment::query()->create([
            'booking_id' => $cancelledBooking->id,
            'amount' => number_format($cancelledBooking->quantity * (float) $cancelledTicket->price, 2, '.', ''),
            'status' => PaymentStatus::FAILED->value,
        ]);
    }

    private function syncRemainingTicketQuantities(): void
    {
        $confirmedByTicket = Booking::query()
            ->selectRaw('ticket_id, SUM(quantity) as confirmed_quantity')
            ->where('status', BookingStatus::CONFIRMED->value)
            ->groupBy('ticket_id')
            ->get()
            ->keyBy('ticket_id');

        Ticket::query()->each(function (Ticket $ticket) use ($confirmedByTicket): void {
            $confirmedQuantity = (int) optional($confirmedByTicket->get($ticket->id))->confirmed_quantity;
            $ticket->update(['quantity' => max(0, 50 - $confirmedQuantity)]);
        });
    }
}
