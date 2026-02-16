<?php

namespace App\Modules\Event\Domain\Queries;

class EventListQuery
{
    public function __construct(
        public readonly int $page,
        public readonly ?string $date,
        public readonly ?string $search,
        public readonly ?string $location,
    ) {
    }

    public function hasOnlyPageFilter(): bool
    {
        return $this->date === null
            && $this->search === null
            && $this->location === null;
    }
}
