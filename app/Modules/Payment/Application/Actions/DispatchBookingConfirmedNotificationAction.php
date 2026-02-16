<?php

namespace App\Modules\Payment\Application\Actions;

use App\Modules\Booking\Domain\Models\Booking;
use App\Modules\Payment\Domain\Models\Payment;
use App\Modules\User\Domain\Models\User;
use App\Modules\Booking\Infrastructure\Notifications\BookingConfirmedNotification;

class DispatchBookingConfirmedNotificationAction
{
    /**
     * @param  array{booking_id: int, event_title: string|null, ticket_type: string|null, quantity: int}  $notificationPayload
     */
    public function execute(Payment $payment, array $notificationPayload): void
    {
        $booking = $payment->{Payment::REL_BOOKING}->load(Booking::REL_USER);
        $bookingUser = $booking->{Booking::REL_USER};

        if (! $bookingUser instanceof User) {
            return;
        }

        $bookingUser->notify(new BookingConfirmedNotification(
            $notificationPayload['booking_id'],
            $notificationPayload['event_title'],
            $notificationPayload['ticket_type'],
            $notificationPayload['quantity'],
        ));
    }
}
