<?php

namespace App\Modules\Payment\Presentation\Http\Resources;

use App\Modules\Payment\Domain\Enums\PaymentStatus;
use App\Modules\Payment\Domain\Models\Payment;
use App\Modules\Booking\Presentation\Http\Resources\BookingResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Payment */
class PaymentResource extends JsonResource
{
    private const OUT_ID = Payment::COL_ID;
    private const OUT_BOOKING_ID = Payment::COL_BOOKING_ID;
    private const OUT_AMOUNT = Payment::COL_AMOUNT;
    private const OUT_STATUS = Payment::COL_STATUS;
    private const OUT_BOOKING = Payment::REL_BOOKING;
    private const OUT_CREATED_AT = Payment::COL_CREATED_AT;
    private const OUT_UPDATED_AT = Payment::COL_UPDATED_AT;

    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $status = $this->status instanceof PaymentStatus ? $this->status->value : (string) $this->status;

        return [
            self::OUT_ID => $this->{Payment::COL_ID},
            self::OUT_BOOKING_ID => $this->{Payment::COL_BOOKING_ID},
            self::OUT_AMOUNT => $this->{Payment::COL_AMOUNT},
            self::OUT_STATUS => $status,
            self::OUT_BOOKING => new BookingResource($this->whenLoaded(Payment::REL_BOOKING)),
            self::OUT_CREATED_AT => $this->{Payment::COL_CREATED_AT},
            self::OUT_UPDATED_AT => $this->{Payment::COL_UPDATED_AT},
        ];
    }
}
