<?php

namespace App\DTO\Dialog\Response;

use App\DTO\Dialog\Message;
use App\DTO\Post\Post;
use App\Entity\Dialog\Message as MessageDoctrine;

class ListResponse
{
    /**
     * @var Message[]
     */
    public array $messages;

    /**
     * @param Post[] $messages
     */
    public function __construct(array $messages)
    {
        $this->messages = $messages;
    }

    /**
     * @param MessageDoctrine[] $doctrineMessages
     * @return self
     */
    public static function createFromResult(array $doctrineMessages): self
    {
        $messages = [];

        foreach ($doctrineMessages as $doctrineMessage) {
            $messages[] = Message::createFromMessage($doctrineMessage);
        }

        return new self($messages);
    }
}