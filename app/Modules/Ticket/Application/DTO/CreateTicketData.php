<?php

namespace App\Modules\Ticket\Application\DTO;

use App\Modules\Ticket\Domain\Models\Ticket;

class CreateTicketData
{
    public const INPUT_TYPE = Ticket::COL_TYPE;
    public const INPUT_PRICE = Ticket::COL_PRICE;
    public const INPUT_QUANTITY = Ticket::COL_QUANTITY;

    public function __construct(
        public readonly string $type,
        public readonly float $price,
        public readonly int $quantity,
    ) {
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            type: (string) $data[self::INPUT_TYPE],
            price: (float) $data[self::INPUT_PRICE],
            quantity: (int) $data[self::INPUT_QUANTITY],
        );
    }
}
