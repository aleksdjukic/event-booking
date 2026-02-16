<?php

namespace App\Modules\Booking\Presentation\Http\Resources;

use App\Modules\Booking\Domain\Enums\BookingStatus;
use App\Modules\Booking\Domain\Models\Booking;
use App\Modules\Payment\Presentation\Http\Resources\PaymentResource;
use App\Modules\Ticket\Presentation\Http\Resources\TicketResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Booking */
class BookingResource extends JsonResource
{
    private const OUT_ID = Booking::COL_ID;
    private const OUT_USER_ID = Booking::COL_USER_ID;
    private const OUT_TICKET_ID = Booking::COL_TICKET_ID;
    private const OUT_QUANTITY = Booking::COL_QUANTITY;
    private const OUT_STATUS = Booking::COL_STATUS;
    private const OUT_TICKET = Booking::REL_TICKET;
    private const OUT_PAYMENT = Booking::REL_PAYMENT;
    private const OUT_CREATED_AT = Booking::COL_CREATED_AT;
    private const OUT_UPDATED_AT = Booking::COL_UPDATED_AT;

    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $status = $this->status instanceof BookingStatus ? $this->status->value : (string) $this->status;

        return [
            self::OUT_ID => $this->{Booking::COL_ID},
            self::OUT_USER_ID => $this->{Booking::COL_USER_ID},
            self::OUT_TICKET_ID => $this->{Booking::COL_TICKET_ID},
            self::OUT_QUANTITY => $this->{Booking::COL_QUANTITY},
            self::OUT_STATUS => $status,
            self::OUT_TICKET => new TicketResource($this->whenLoaded(Booking::REL_TICKET)),
            self::OUT_PAYMENT => new PaymentResource($this->whenLoaded(Booking::REL_PAYMENT)),
            self::OUT_CREATED_AT => $this->{Booking::COL_CREATED_AT},
            self::OUT_UPDATED_AT => $this->{Booking::COL_UPDATED_AT},
        ];
    }
}
