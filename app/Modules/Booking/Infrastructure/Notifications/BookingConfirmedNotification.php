<?php

namespace App\Modules\Booking\Infrastructure\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class BookingConfirmedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public const PAYLOAD_BOOKING_ID = 'booking_id';
    public const PAYLOAD_EVENT_TITLE = 'event_title';
    public const PAYLOAD_TICKET_TYPE = 'ticket_type';
    public const PAYLOAD_QUANTITY = 'quantity';

    private const CHANNEL_DATABASE = 'database';

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
        return [self::CHANNEL_DATABASE];
    }

    /**
     * @return array<string, int|string|null>
     */
    public function toArray(object $notifiable): array
    {
        return [
            self::PAYLOAD_BOOKING_ID => $this->bookingId,
            self::PAYLOAD_EVENT_TITLE => $this->eventTitle,
            self::PAYLOAD_TICKET_TYPE => $this->ticketType,
            self::PAYLOAD_QUANTITY => $this->quantity,
        ];
    }
}
