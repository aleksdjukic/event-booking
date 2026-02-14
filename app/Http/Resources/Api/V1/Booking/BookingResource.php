<?php

namespace App\Http\Resources\Api\V1\Booking;

use App\Domain\Booking\Enums\BookingStatus;
use App\Domain\Booking\Models\Booking;
use App\Http\Resources\Api\V1\Payment\PaymentResource;
use App\Http\Resources\Api\V1\Ticket\TicketResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Booking */
class BookingResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $status = $this->status instanceof BookingStatus ? $this->status->value : (string) $this->status;

        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'ticket_id' => $this->ticket_id,
            'quantity' => $this->quantity,
            'status' => $status,
            'ticket' => new TicketResource($this->whenLoaded('ticket')),
            'payment' => new PaymentResource($this->whenLoaded('payment')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
