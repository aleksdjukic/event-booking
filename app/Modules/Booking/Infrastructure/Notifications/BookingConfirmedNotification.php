<?php

namespace App\Modules\Booking\Infrastructure\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class BookingConfirmedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly int $bookingId,
        private readonly ?string $eventTitle,
        private readonly ?string $ticketType,
        private readonly int $quantity,
    ) {
    }

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * @return array<string, int|string|null>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'booking_id' => $this->bookingId,
            'event_title' => $this->eventTitle,
            'ticket_type' => $this->ticketType,
            'quantity' => $this->quantity,
        ];
    }
}
