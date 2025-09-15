<?php

namespace App\DTO\Dialog;

use App\Entity\Dialog\Message as DoctrineMessage;

class Message
{
    private const string DATE_FORMAT = 'Y-m-d H:i:s';

    public string $userId;
    public string $text;
    public string $time;

    private function __construct(string $userId, string $text, string $time)
    {
        $this->userId = $userId;
        $this->text = $text;
        $this->time = $time;
    }

    public static function createFromMessage(DoctrineMessage $message): self
    {
        return new self(
            $message->getUserId(),
            $message->getContent(),
            $message->getCreatedAt()->format(self::DATE_FORMAT)
        );
    }
}