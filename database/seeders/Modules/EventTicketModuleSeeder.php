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
            ->whereIn('email', ['organizer1@example.com', 'organizer2@example.com', 'organizer3@example.com'])
            ->get()
            ->keyBy('email');

        $events = [
            [
                'title' => 'Tech Conference 2026',
                'description' => 'Annual software and cloud conference.',
                'date' => '2026-06-10 10:00:00',
                'location' => 'Belgrade',
                'created_by' => $organizers->get('organizer1@example.com')?->id,
            ],
            [
                'title' => 'Startup Networking Night',
                'description' => null,
                'date' => '2026-07-05 19:30:00',
                'location' => 'Novi Sad',
                'created_by' => $organizers->get('organizer2@example.com')?->id,
            ],
            [
                'title' => 'Music Open Air',
                'description' => 'Outdoor live performances.',
                'date' => '2026-08-12 18:00:00',
                'location' => 'Nis',
                'created_by' => $organizers->get('organizer3@example.com')?->id,
            ],
            [
                'title' => 'Business Expo',
                'description' => 'Exhibition for local businesses.',
                'date' => '2026-09-20 09:00:00',
                'location' => 'Belgrade',
                'created_by' => $organizers->get('organizer1@example.com')?->id,
            ],
            [
                'title' => 'Design Workshop',
                'description' => 'Hands-on product design workshop.',
                'date' => '2026-10-02 11:00:00',
                'location' => 'Kragujevac',
                'created_by' => $organizers->get('organizer2@example.com')?->id,
            ],
        ];

        foreach ($events as $eventData) {
            $event = Event::query()->updateOrCreate(
                ['title' => $eventData['title'], 'date' => $eventData['date']],
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
                ['event_id' => $eventId, 'type' => $type],
                ['price' => $price, 'quantity' => 50]
            );
        }
    }
}
