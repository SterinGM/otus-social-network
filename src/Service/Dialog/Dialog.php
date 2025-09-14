<?php

namespace App\Service\Dialog;

use App\DTO\Dialog\Request\SendRequest;
use App\Entity\Dialog\Chat;
use App\Repository\Dialog\ChatRepository;
use App\Repository\Dialog\MessageRepository;
use App\Repository\Main\UserRepository;
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

    public function sendMessage(SendRequest $sendRequest): void
    {
        if ($sendRequest->userId === $sendRequest->fromUserId) {
            return;
        }

        $user = $this->userRepository->getById($sendRequest->userId);

        if ($user === null) {
            return;
        }

        $chat = $this->getChat($sendRequest->fromUserId, $sendRequest->userId);

        dump($chat);
    }

    private function getChat(string $userId1, string $userId2): Chat
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
}