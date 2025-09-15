<?php

namespace App\DTO\Dialog\Request;

class ListRequest
{
    public const string FIELD_CHAT_ID = 'chat_id';

    public string $chatId;

    private function __construct(string $chatId)
    {
        $this->chatId = $chatId;
    }

    public static function createFromArray(array $data): self
    {
        return new self(
            $data[self::FIELD_CHAT_ID],
        );
    }
}