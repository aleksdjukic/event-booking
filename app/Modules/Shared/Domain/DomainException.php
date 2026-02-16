<?php

namespace App\Modules\Shared\Domain;

use RuntimeException;

class DomainException extends RuntimeException
{
    public function __construct(private readonly DomainError $error)
    {
        parent::__construct($error->message());
    }

    public function error(): DomainError
    {
        return $this->error;
    }
}
