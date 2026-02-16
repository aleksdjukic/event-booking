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
            ->where(User::COL_EMAIL, 'like', 'customer%@example.com')
            ->orderBy(User::COL_EMAIL)
            ->limit(10)
            ->get();

        $tickets = Ticket::query()->orderBy(Ticket::COL_ID)->get();

        Payment::query()->delete();
        Booking::query()->delete();
        Ticket::query()->update([Ticket::COL_QUANTITY => 50]);

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
                Booking::COL_USER_ID => $customers[$plan['customer_index']]->{User::COL_ID},
                Booking::COL_TICKET_ID => $tickets[$plan['ticket_index']]->{Ticket::COL_ID},
                Booking::COL_QUANTITY => $plan['quantity'],
                Booking::COL_STATUS => $plan['status']->value,
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
            $ticket = $tickets->firstWhere(Ticket::COL_ID, $booking->{Booking::COL_TICKET_ID});
            if (! $ticket instanceof Ticket) {
                continue;
            }

            Payment::query()->create([
                Payment::COL_BOOKING_ID => $booking->{Booking::COL_ID},
                Payment::COL_AMOUNT => number_format($booking->{Booking::COL_QUANTITY} * (float) $ticket->{Ticket::COL_PRICE}, 2, '.', ''),
                Payment::COL_STATUS => PaymentStatus::SUCCESS->value,
            ]);
        }

        $cancelledBooking = $bookings->first(
            fn (Booking $booking): bool => $booking->status === BookingStatus::CANCELLED
        );

        if (! $cancelledBooking instanceof Booking) {
            return;
        }

        $cancelledTicket = $tickets->firstWhere(Ticket::COL_ID, $cancelledBooking->{Booking::COL_TICKET_ID});
        if (! $cancelledTicket instanceof Ticket) {
            return;
        }

        Payment::query()->create([
            Payment::COL_BOOKING_ID => $cancelledBooking->{Booking::COL_ID},
            Payment::COL_AMOUNT => number_format($cancelledBooking->{Booking::COL_QUANTITY} * (float) $cancelledTicket->{Ticket::COL_PRICE}, 2, '.', ''),
            Payment::COL_STATUS => PaymentStatus::FAILED->value,
        ]);
    }

    private function syncRemainingTicketQuantities(): void
    {
        $confirmedByTicket = Booking::query()
            ->selectRaw(Booking::COL_TICKET_ID.', SUM('.Booking::COL_QUANTITY.') as confirmed_quantity')
            ->where(Booking::COL_STATUS, BookingStatus::CONFIRMED->value)
            ->groupBy(Booking::COL_TICKET_ID)
            ->get()
            ->keyBy(fn (Booking $booking): int => (int) $booking->{Booking::COL_TICKET_ID});

        Ticket::query()->each(function (Ticket $ticket) use ($confirmedByTicket): void {
            $confirmedQuantity = (int) optional($confirmedByTicket->get((int) $ticket->{Ticket::COL_ID}))->confirmed_quantity;
            $ticket->update([Ticket::COL_QUANTITY => max(0, 50 - $confirmedQuantity)]);
        });
    }
}
