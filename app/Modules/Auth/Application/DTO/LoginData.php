<?php

namespace App\Modules\Auth\Application\DTO;

class LoginData
{
    public const INPUT_EMAIL = 'email';
    public const INPUT_PASSWORD = 'password';

    public function __construct(
        public readonly string $email,
        public readonly string $password,
    ) {
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            email: (string) $data[self::INPUT_EMAIL],
            password: (string) $data[self::INPUT_PASSWORD],
        );
    }
}
