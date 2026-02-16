<?php

namespace App\Modules\Auth\Application\DTO;

class RegisterData
{
    public const INPUT_NAME = 'name';
    public const INPUT_EMAIL = 'email';
    public const INPUT_PASSWORD = 'password';
    public const INPUT_PHONE = 'phone';

    public function __construct(
        public readonly string $name,
        public readonly string $email,
        public readonly string $password,
        public readonly ?string $phone,
    ) {
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            name: (string) $data[self::INPUT_NAME],
            email: (string) $data[self::INPUT_EMAIL],
            password: (string) $data[self::INPUT_PASSWORD],
            phone: isset($data[self::INPUT_PHONE]) ? (string) $data[self::INPUT_PHONE] : null,
        );
    }
}
