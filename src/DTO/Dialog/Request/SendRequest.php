<?php

namespace App\DTO\Dialog\Request;

class SendRequest
{
    public const string FIELD_CHAT_ID = 'chat_id';
    public const string FIELD_TEXT = 'text';

    public string $chatId;
    public string $test;

    private function __construct(string $chatId, string $test)
    {
        $this->chatId = $chatId;
        $this->test = $test;
    }

    public static function createFromArray(array $data): self
    {
        return new self(
            $data[self::FIELD_CHAT_ID],
            $data[self::FIELD_TEXT],
        );
    }
}