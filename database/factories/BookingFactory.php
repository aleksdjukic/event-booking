<?php

namespace Database\Factories;

use App\Modules\Booking\Domain\Enums\BookingStatus;
use App\Modules\User\Domain\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Modules\Booking\Domain\Models\Booking>
 */
class BookingFactory extends Factory
{
    protected $model = \App\Modules\Booking\Domain\Models\Booking::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory()->customer(),
            'ticket_id' => TicketFactory::new(),
            'quantity' => fake()->numberBetween(1, 5),
            'status' => fake()->randomElement([
                BookingStatus::PENDING->value,
                BookingStatus::PENDING->value,
                BookingStatus::CONFIRMED->value,
                BookingStatus::CANCELLED->value,
            ]),
        ];
    }
}
