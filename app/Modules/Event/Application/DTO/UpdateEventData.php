<?php

namespace App\Modules\Event\Application\DTO;

use App\Modules\Event\Domain\Models\Event;

class UpdateEventData
{
    public const INPUT_TITLE = Event::COL_TITLE;
    public const INPUT_DESCRIPTION = Event::COL_DESCRIPTION;
    public const INPUT_DATE = Event::COL_DATE;
    public const INPUT_LOCATION = Event::COL_LOCATION;

    public function __construct(
        public readonly string $title,
        public readonly ?string $description,
        public readonly string $date,
        public readonly string $location,
    ) {
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            title: (string) $data[self::INPUT_TITLE],
            description: isset($data[self::INPUT_DESCRIPTION]) ? (string) $data[self::INPUT_DESCRIPTION] : null,
            date: (string) $data[self::INPUT_DATE],
            location: (string) $data[self::INPUT_LOCATION],
        );
    }
}
