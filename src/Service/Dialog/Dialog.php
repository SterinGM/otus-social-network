<?php

namespace App\Service\Dialog;

use App\Entity\Dialog\Chat;
use App\Entity\Dialog\Message;
use App\Repository\Dialog\ChatRepository;
use App\Repository\Dialog\MessageRepository;
use App\Repository\Main\UserRepository;
use App\Service\Exception\ChatNotFoundException;
use App\Service\Exception\UserNotFoundException;
use Symfony\Component\Uid\Uuid;

class Dialog implements DialogInterface
{
    private UserRepository $userRepository;
    private ChatRepository $chatRepository;
    private MessageRepository $messageRepository;

    public function __construct(
        UserRepository    $userRepository,
        ChatRepository    $chatRepository,
        MessageRepository $messageRepository,
    ) {
        $this->userRepository = $userRepository;
        $this->chatRepository = $chatRepository;
        $this->messageRepository = $messageRepository;
    }

    public function createChat(array $userIds): Chat
    {
        $userIds = array_unique($userIds);

        foreach ($userIds as $userId) {
            $user = $this->userRepository->getById($userId);

            if ($user === null) {
                throw new UserNotFoundException($userId);
            }
        }

        $chat = $this->buildChat($userIds);

        $this->chatRepository->createChat($chat);

        return $chat;
    }

    public function getChatById(string $chatId, string $userId): Chat
    {
        $chat = $this->chatRepository->getChatById($chatId);

        if ($chat === null) {
            throw new ChatNotFoundException($chatId);
        }

        if (!in_array($userId, (array)$chat->getUserIds())) {
            throw new ChatNotFoundException($chatId);
        }

        return $chat;
    }

    public function sendMessage(Chat $chat, string $userId, string $text): Message
    {
        if (!in_array($userId, (array)$chat->getUserIds())) {
            throw new ChatNotFoundException($chat->getId());
        }

        $message = $this->buildMessage($chat, $userId, $text);

        $this->messageRepository->createMessage($message);

        return $message;
    }

    public function getMessages(Chat $chat, string $userId): array
    {
        if (!in_array($userId, (array)$chat->getUserIds())) {
            throw new ChatNotFoundException($chat->getId());
        }

        return $this->messageRepository->getAllMessages($chat);
    }

    public function buildMessage(Chat $chat, string $userId, string $text): Message
    {
        return new Message()
            ->setId(Uuid::v7()->toRfc4122())
            ->setChat($chat)
            ->setContent($text)
            ->setUserId($userId);
    }

    /**
     * @param string[] $userIds
     */
    public function buildChat(array $userIds): Chat
    {
        return new Chat()
            ->setId(Uuid::v7()->toRfc4122())
            ->setUserIds($userIds);
    }
}