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

    public function getChatById(string $chatId): Chat
    {
        $chat = $this->chatRepository->getChatById($chatId);

        if ($chat === null) {
            throw new ChatNotFoundException($chatId);
        }

        return $chat;
    }

    public function sendMessage(Chat $chat, string $userId, string $text): Message
    {
        $message = $this->buildMessage($chat, $userId, $text);

        $this->messageRepository->createMessage($message);

        return $message;
    }

    public function getMessages(Chat $chat): array
    {
        return $this->messageRepository->getMessages($chat);
    }

    private function getOrCreateChat(string $userId1, string $userId2): Chat
    {
        $chat = $this->chatRepository->getChatByUsers($userId1, $userId2);

        if ($chat == null) {
            $chat = new Chat()
                ->setId(Uuid::v7()->toRfc4122())
                ->setUserIds([$userId1, $userId2]);

            $this->chatRepository->createChat($chat);
        }

        return $chat;
    }

    public function buildMessage(Chat $chat, string $userId, string $text): Message
    {
        return new Message()
            ->setId(Uuid::v7()->toRfc4122())
            ->setChat($chat)
            ->setContent($text)
            ->setUserId($userId);
    }
}