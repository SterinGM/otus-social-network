<?php

namespace App\DTO\Auth\Request;

class LoginRequest
{
    public const string FIELD_ID = 'id';
    public const string FIELD_PASSWORD = 'password';

    public string $userId;
    public string $password;

    private function __construct(string $userId, string $password)
    {
        $this->userId = $userId;
        $this->password = $password;
    }

    public static function createFromArray(array $data): self
    {
        return new self(
            $data[self::FIELD_ID],
            $data[self::FIELD_PASSWORD],
        );
    }
}