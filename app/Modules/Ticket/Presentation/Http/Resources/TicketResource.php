<?php

namespace App\Modules\Ticket\Presentation\Http\Resources;

use App\Modules\Ticket\Domain\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Ticket */
class TicketResource extends JsonResource
{
    private const OUT_ID = Ticket::COL_ID;
    private const OUT_EVENT_ID = Ticket::COL_EVENT_ID;
    private const OUT_TYPE = Ticket::COL_TYPE;
    private const OUT_PRICE = Ticket::COL_PRICE;
    private const OUT_QUANTITY = Ticket::COL_QUANTITY;
    private const OUT_CREATED_AT = Ticket::COL_CREATED_AT;
    private const OUT_UPDATED_AT = Ticket::COL_UPDATED_AT;

    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            self::OUT_ID => $this->{Ticket::COL_ID},
            self::OUT_EVENT_ID => $this->{Ticket::COL_EVENT_ID},
            self::OUT_TYPE => $this->{Ticket::COL_TYPE},
            self::OUT_PRICE => $this->{Ticket::COL_PRICE},
            self::OUT_QUANTITY => $this->{Ticket::COL_QUANTITY},
            self::OUT_CREATED_AT => $this->{Ticket::COL_CREATED_AT},
            self::OUT_UPDATED_AT => $this->{Ticket::COL_UPDATED_AT},
        ];
    }
}
