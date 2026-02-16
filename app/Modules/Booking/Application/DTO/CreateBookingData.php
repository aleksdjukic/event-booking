<?php

namespace App\Modules\Booking\Application\DTO;

class CreateBookingData
{
    public const INPUT_QUANTITY = 'quantity';

    public function __construct(public readonly int $quantity)
    {
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(quantity: (int) $data[self::INPUT_QUANTITY]);
    }
}
