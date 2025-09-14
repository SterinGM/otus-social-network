<?php

namespace App\Service\Dialog;

use App\DTO\Dialog\Request\ListRequest;
use App\DTO\Dialog\Request\SendRequest;
use App\Entity\Dialog\Chat;
use App\Entity\Dialog\Message;
use App\Repository\Dialog\ChatRepository;
use App\Repository\Dialog\MessageRepository;
use App\Repository\Main\UserRepository;
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

    public function sendMessage(SendRequest $sendRequest): void
    {
        $user = $this->userRepository->getById($sendRequest->userId);

        if ($user === null) {
            throw new UserNotFoundException($sendRequest->userId);
        }

        $chat = $this->getOrCreateChat($sendRequest->fromUserId, $sendRequest->userId);

        $message = $this->getMessage($chat, $sendRequest);

        $this->messageRepository->createMessage($message);
    }

    public function getMessages(ListRequest $listRequest): array
    {
        $user = $this->userRepository->getById($listRequest->userId);

        if ($user === null) {
            throw new UserNotFoundException($listRequest->userId);
        }

        $chat = $this->getOrCreateChat($listRequest->fromUserId, $listRequest->userId);

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

    public function getMessage(Chat $chat, SendRequest $sendRequest): Message
    {
        return new Message()
            ->setId(Uuid::v7()->toRfc4122())
            ->setChat($chat)
            ->setContent($sendRequest->test)
            ->setUserId($sendRequest->fromUserId);
    }
}