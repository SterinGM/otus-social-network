<?php

namespace App\DTO\Dialog\Response;

use App\Entity\Dialog\Chat;

class NewResponse
{
    public string $chatId;
    /** @var string[] $userIds */
    public array $userIds;

    /**
     * @param string[] $userIds
     */
    public function __construct(string $chatId, array $userIds)
    {
        $this->chatId = $chatId;
        $this->userIds = $userIds;
    }

    public static function createFromChat(Chat $chat): self
    {
        return new self($chat->getId(), $chat->getUserIds());
    }
}