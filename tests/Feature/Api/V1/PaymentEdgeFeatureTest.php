<?php

namespace Tests\Feature\Api\V1;

use App\Models\Booking;
use App\Models\Event;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class PaymentEdgeFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_duplicate_payment_returns_409(): void
    {
        Notification::fake();

        $customer = $this->createUser('customer', 'payment.duplicate.customer@example.com');
        $booking = $this->createPendingBooking($customer, 10, 2);

        Sanctum::actingAs($customer);

        $this->postJson('/api/v1/bookings/'.$booking->id.'/payment', [
            'force_success' => true,
        ])->assertStatus(201);

        $this->postJson('/api/v1/bookings/'.$booking->id.'/payment', [
            'force_success' => true,
        ])->assertStatus(409)
            ->assertJsonPath('success', false);
    }

    public function test_sold_out_and_not_enough_inventory_return_409(): void
    {
        Notification::fake();

        $customer = $this->createUser('customer', 'payment.inventory.customer@example.com');

        $soldOutBooking = $this->createPendingBooking($customer, 0, 1);
        $notEnoughBooking = $this->createPendingBooking($customer, 1, 2);

        Sanctum::actingAs($customer);

        $this->postJson('/api/v1/bookings/'.$soldOutBooking->id.'/payment', [
            'force_success' => true,
        ])->assertStatus(409)
            ->assertJsonPath('message', 'Ticket is sold out.');

        $this->postJson('/api/v1/bookings/'.$notEnoughBooking->id.'/payment', [
            'force_success' => true,
        ])->assertStatus(409)
            ->assertJsonPath('message', 'Not enough ticket inventory.');
    }

    private function createUser(string $role, string $email): User
    {
        $user = new User();
        $user->name = ucfirst($role).' User';
        $user->email = $email;
        $user->password = Hash::make('password123');
        $user->role = $role;
        $user->save();

        return $user;
    }

    private function createPendingBooking(User $customer, int $ticketQuantity, int $bookingQuantity): Booking
    {
        static $organizerIndex = 0;
        $organizerIndex++;

        $organizer = $this->createUser('organizer', 'payment.organizer.'.$organizerIndex.'@example.com');

        $event = new Event();
        $event->title = 'Payment Edge Event';
        $event->description = null;
        $event->date = '2026-10-01 10:00:00';
        $event->location = 'Novi Sad';
        $event->created_by = $organizer->id;
        $event->save();

        $ticket = new Ticket();
        $ticket->event_id = $event->id;
        $ticket->type = 'Standard';
        $ticket->price = '80.00';
        $ticket->quantity = $ticketQuantity;
        $ticket->save();

        $booking = new Booking();
        $booking->user_id = $customer->id;
        $booking->ticket_id = $ticket->id;
        $booking->quantity = $bookingQuantity;
        $booking->status = 'pending';
        $booking->save();

        return $booking;
    }
}
