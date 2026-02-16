<?php

namespace Database\Seeders\Modules;

use App\Modules\Event\Domain\Models\Event;
use App\Modules\Ticket\Domain\Models\Ticket;
use App\Modules\User\Domain\Models\User;
use Illuminate\Database\Seeder;

class EventTicketModuleSeeder extends Seeder
{
    public function run(): void
    {
        $organizers = User::query()
            ->whereIn(User::COL_EMAIL, ['organizer1@example.com', 'organizer2@example.com', 'organizer3@example.com'])
            ->get()
            ->keyBy(fn (User $user): string => (string) $user->{User::COL_EMAIL});

        $events = [
            [
                Event::COL_TITLE => 'Tech Conference 2026',
                Event::COL_DESCRIPTION => 'Annual software and cloud conference.',
                Event::COL_DATE => '2026-06-10 10:00:00',
                Event::COL_LOCATION => 'Belgrade',
                Event::COL_CREATED_BY => $organizers->get('organizer1@example.com')?->{User::COL_ID},
            ],
            [
                Event::COL_TITLE => 'Startup Networking Night',
                Event::COL_DESCRIPTION => null,
                Event::COL_DATE => '2026-07-05 19:30:00',
                Event::COL_LOCATION => 'Novi Sad',
                Event::COL_CREATED_BY => $organizers->get('organizer2@example.com')?->{User::COL_ID},
            ],
            [
                Event::COL_TITLE => 'Music Open Air',
                Event::COL_DESCRIPTION => 'Outdoor live performances.',
                Event::COL_DATE => '2026-08-12 18:00:00',
                Event::COL_LOCATION => 'Nis',
                Event::COL_CREATED_BY => $organizers->get('organizer3@example.com')?->{User::COL_ID},
            ],
            [
                Event::COL_TITLE => 'Business Expo',
                Event::COL_DESCRIPTION => 'Exhibition for local businesses.',
                Event::COL_DATE => '2026-09-20 09:00:00',
                Event::COL_LOCATION => 'Belgrade',
                Event::COL_CREATED_BY => $organizers->get('organizer1@example.com')?->{User::COL_ID},
            ],
            [
                Event::COL_TITLE => 'Design Workshop',
                Event::COL_DESCRIPTION => 'Hands-on product design workshop.',
                Event::COL_DATE => '2026-10-02 11:00:00',
                Event::COL_LOCATION => 'Kragujevac',
                Event::COL_CREATED_BY => $organizers->get('organizer2@example.com')?->{User::COL_ID},
            ],
        ];

        foreach ($events as $eventData) {
            $event = Event::query()->updateOrCreate(
                [Event::COL_TITLE => $eventData[Event::COL_TITLE], Event::COL_DATE => $eventData[Event::COL_DATE]],
                $eventData
            );

            $this->seedEventTickets($event->id);
        }
    }

    private function seedEventTickets(int $eventId): void
    {
        $ticketTypePrice = [
            'VIP' => '120.00',
            'Standard' => '70.00',
            'Regular' => '40.00',
        ];

        foreach ($ticketTypePrice as $type => $price) {
            Ticket::query()->updateOrCreate(
                [Ticket::COL_EVENT_ID => $eventId, Ticket::COL_TYPE => $type],
                [Ticket::COL_PRICE => $price, Ticket::COL_QUANTITY => 50]
            );
        }
    }
}
