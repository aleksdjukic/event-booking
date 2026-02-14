<?php

namespace App\Services\Support;

use RuntimeException;

class ServiceException extends RuntimeException
{
    public function __construct(
        string $message,
        private readonly int $status,
    ) {
        parent::__construct($message);
    }

    public function status(): int
    {
        return $this->status;
    }
}
