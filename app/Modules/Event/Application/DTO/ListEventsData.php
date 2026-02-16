<?php

namespace App\Modules\Event\Application\DTO;

class ListEventsData
{
    public const INPUT_PAGE = 'page';
    public const INPUT_DATE = 'date';
    public const INPUT_SEARCH = 'search';
    public const INPUT_LOCATION = 'location';

    public function __construct(
        public readonly int $page,
        public readonly ?string $date,
        public readonly ?string $search,
        public readonly ?string $location,
    ) {
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            page: max(1, (int) ($data[self::INPUT_PAGE] ?? 1)),
            date: isset($data[self::INPUT_DATE]) ? (string) $data[self::INPUT_DATE] : null,
            search: isset($data[self::INPUT_SEARCH]) ? (string) $data[self::INPUT_SEARCH] : null,
            location: isset($data[self::INPUT_LOCATION]) ? (string) $data[self::INPUT_LOCATION] : null,
        );
    }
}
