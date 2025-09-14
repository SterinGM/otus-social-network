<?php

namespace App\DTO\Dialog\Request;

class ListRequest
{
    public const string FIELD_USER_ID = 'user_id';

    public string $userId;
    public string $fromUserId;

    private function __construct(string $userId)
    {
        $this->userId = $userId;
    }

    public static function createFromArray(array $data): self
    {
        return new self(
            $data[self::FIELD_USER_ID],
        );
    }
}