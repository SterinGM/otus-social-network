<?php

namespace App\DTO\Dialog\Response;

use App\DTO\Dialog\Message;
use App\Entity\Dialog\Chat;
use App\Entity\Dialog\Message as MessageDoctrine;

class ListResponse
{
    public string $chatId;
    /** @var string[] $userIds */
    public array $userIds;
    /** @var Message[] */
    public array $messages;

    /**
     * @param string[] $userIds
     * @param Message[] $messages
     */
    public function __construct(string $chatId, array $userIds, array $messages)
    {
        $this->chatId = $chatId;
        $this->userIds = $userIds;
        $this->messages = $messages;
    }

    /**
     * @param MessageDoctrine[] $doctrineMessages
     */
    public static function createFromResult(Chat $chat, array $doctrineMessages): self
    {
        $messages = [];

        foreach ($doctrineMessages as $doctrineMessage) {
            $messages[] = Message::createFromMessage($doctrineMessage);
        }

        return new self($chat->getId(), $chat->getUserIds(), $messages);
    }
}