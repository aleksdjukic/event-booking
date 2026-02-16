<?php

namespace App\Modules\Event\Presentation\Http\Resources;

use App\Modules\Event\Domain\Models\Event;
use App\Modules\Ticket\Presentation\Http\Resources\TicketResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Event */
class EventResource extends JsonResource
{
    private const OUT_ID = Event::COL_ID;
    private const OUT_TITLE = Event::COL_TITLE;
    private const OUT_DESCRIPTION = Event::COL_DESCRIPTION;
    private const OUT_DATE = Event::COL_DATE;
    private const OUT_LOCATION = Event::COL_LOCATION;
    private const OUT_CREATED_BY = Event::COL_CREATED_BY;
    private const OUT_TICKETS = Event::REL_TICKETS;
    private const OUT_CREATED_AT = Event::COL_CREATED_AT;
    private const OUT_UPDATED_AT = Event::COL_UPDATED_AT;

    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            self::OUT_ID => $this->{Event::COL_ID},
            self::OUT_TITLE => $this->{Event::COL_TITLE},
            self::OUT_DESCRIPTION => $this->{Event::COL_DESCRIPTION},
            self::OUT_DATE => $this->{Event::COL_DATE},
            self::OUT_LOCATION => $this->{Event::COL_LOCATION},
            self::OUT_CREATED_BY => $this->{Event::COL_CREATED_BY},
            self::OUT_TICKETS => TicketResource::collection($this->whenLoaded(Event::REL_TICKETS)),
            self::OUT_CREATED_AT => $this->{Event::COL_CREATED_AT},
            self::OUT_UPDATED_AT => $this->{Event::COL_UPDATED_AT},
        ];
    }
}
