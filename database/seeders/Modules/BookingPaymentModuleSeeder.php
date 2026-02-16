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
    private const PLAN_TICKET_INDEX = 'ticket_index';
    private const PLAN_CUSTOMER_INDEX = 'customer_index';
    private const PLAN_QUANTITY = Booking::COL_QUANTITY;
    private const PLAN_STATUS = Booking::COL_STATUS;

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
            [self::PLAN_TICKET_INDEX => 0, self::PLAN_CUSTOMER_INDEX => 0, self::PLAN_QUANTITY => 2, self::PLAN_STATUS => BookingStatus::CONFIRMED],
            [self::PLAN_TICKET_INDEX => 1, self::PLAN_CUSTOMER_INDEX => 1, self::PLAN_QUANTITY => 1, self::PLAN_STATUS => BookingStatus::PENDING],
            [self::PLAN_TICKET_INDEX => 2, self::PLAN_CUSTOMER_INDEX => 2, self::PLAN_QUANTITY => 3, self::PLAN_STATUS => BookingStatus::CANCELLED],
            [self::PLAN_TICKET_INDEX => 3, self::PLAN_CUSTOMER_INDEX => 3, self::PLAN_QUANTITY => 2, self::PLAN_STATUS => BookingStatus::CONFIRMED],
            [self::PLAN_TICKET_INDEX => 4, self::PLAN_CUSTOMER_INDEX => 4, self::PLAN_QUANTITY => 5, self::PLAN_STATUS => BookingStatus::PENDING],
            [self::PLAN_TICKET_INDEX => 5, self::PLAN_CUSTOMER_INDEX => 5, self::PLAN_QUANTITY => 1, self::PLAN_STATUS => BookingStatus::CONFIRMED],
            [self::PLAN_TICKET_INDEX => 6, self::PLAN_CUSTOMER_INDEX => 6, self::PLAN_QUANTITY => 4, self::PLAN_STATUS => BookingStatus::CANCELLED],
            [self::PLAN_TICKET_INDEX => 7, self::PLAN_CUSTOMER_INDEX => 7, self::PLAN_QUANTITY => 2, self::PLAN_STATUS => BookingStatus::PENDING],
            [self::PLAN_TICKET_INDEX => 8, self::PLAN_CUSTOMER_INDEX => 8, self::PLAN_QUANTITY => 3, self::PLAN_STATUS => BookingStatus::CONFIRMED],
            [self::PLAN_TICKET_INDEX => 9, self::PLAN_CUSTOMER_INDEX => 9, self::PLAN_QUANTITY => 1, self::PLAN_STATUS => BookingStatus::PENDING],
            [self::PLAN_TICKET_INDEX => 10, self::PLAN_CUSTOMER_INDEX => 0, self::PLAN_QUANTITY => 2, self::PLAN_STATUS => BookingStatus::CONFIRMED],
            [self::PLAN_TICKET_INDEX => 11, self::PLAN_CUSTOMER_INDEX => 1, self::PLAN_QUANTITY => 4, self::PLAN_STATUS => BookingStatus::PENDING],
            [self::PLAN_TICKET_INDEX => 12, self::PLAN_CUSTOMER_INDEX => 2, self::PLAN_QUANTITY => 1, self::PLAN_STATUS => BookingStatus::CANCELLED],
            [self::PLAN_TICKET_INDEX => 13, self::PLAN_CUSTOMER_INDEX => 3, self::PLAN_QUANTITY => 3, self::PLAN_STATUS => BookingStatus::CONFIRMED],
            [self::PLAN_TICKET_INDEX => 14, self::PLAN_CUSTOMER_INDEX => 4, self::PLAN_QUANTITY => 2, self::PLAN_STATUS => BookingStatus::PENDING],
            [self::PLAN_TICKET_INDEX => 0, self::PLAN_CUSTOMER_INDEX => 5, self::PLAN_QUANTITY => 1, self::PLAN_STATUS => BookingStatus::PENDING],
            [self::PLAN_TICKET_INDEX => 4, self::PLAN_CUSTOMER_INDEX => 6, self::PLAN_QUANTITY => 2, self::PLAN_STATUS => BookingStatus::CONFIRMED],
            [self::PLAN_TICKET_INDEX => 8, self::PLAN_CUSTOMER_INDEX => 7, self::PLAN_QUANTITY => 1, self::PLAN_STATUS => BookingStatus::PENDING],
            [self::PLAN_TICKET_INDEX => 10, self::PLAN_CUSTOMER_INDEX => 8, self::PLAN_QUANTITY => 2, self::PLAN_STATUS => BookingStatus::CONFIRMED],
            [self::PLAN_TICKET_INDEX => 14, self::PLAN_CUSTOMER_INDEX => 9, self::PLAN_QUANTITY => 3, self::PLAN_STATUS => BookingStatus::CANCELLED],
        ];

        $bookings = collect();

        foreach ($bookingsPlan as $plan) {
            $bookings->push(Booking::query()->create([
                Booking::COL_USER_ID => $customers[$plan[self::PLAN_CUSTOMER_INDEX]]->{User::COL_ID},
                Booking::COL_TICKET_ID => $tickets[$plan[self::PLAN_TICKET_INDEX]]->{Ticket::COL_ID},
                Booking::COL_QUANTITY => $plan[self::PLAN_QUANTITY],
                Booking::COL_STATUS => $plan[self::PLAN_STATUS]->value,
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
