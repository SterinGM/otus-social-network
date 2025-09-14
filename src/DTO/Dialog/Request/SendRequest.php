<?php

namespace App\DTO\Dialog\Request;

class SendRequest
{
    public const string FIELD_USER_ID = 'user_id';
    public const string FIELD_TEXT = 'text';

    public string $userId;
    public string $test;
    public string $fromUserId;

    private function __construct(string $userId, string $test)
    {
        $this->userId = $userId;
        $this->test = $test;
    }

    public static function createFromArray(array $data): self
    {
        return new self(
            $data[self::FIELD_USER_ID],
            $data[self::FIELD_TEXT],
        );
    }
}