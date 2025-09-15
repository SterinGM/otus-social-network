<?php

namespace App\Service\Dialog;

use App\Entity\Dialog\Chat;
use App\Entity\Dialog\Message;

interface DialogInterface
{
    /**
     * @param string[] $userIds
     */
    public function createChat(array $userIds): Chat;

    public function getChatById(string $chatId, string $userId): Chat;

    public function sendMessage(Chat $chat, string $userId, string $text): Message;

    /**
     * @return Message[]
     */
    public function getMessages(Chat $chat, string $userId): array;
}