<?php

namespace App\Service\Friend;

use App\DTO\Friend\Request\DeleteRequest;
use App\DTO\Friend\Request\SetRequest;
use App\Entity\User;
use App\Event\Friend\FriendCreatedEvent;
use App\Event\Friend\FriendDeletedEvent;
use App\Repository\FriendRepository;
use App\Repository\UserRepository;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class Friend implements FriendInterface
{
    private UserRepository $userRepository;
    private FriendRepository $friendRepository;
    private EventDispatcherInterface $eventDispatcher;

    public function __construct(
        UserRepository $userRepository,
        FriendRepository $friendRepository,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->userRepository = $userRepository;
        $this->friendRepository = $friendRepository;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function setUserFriend(SetRequest $setRequest): void
    {
        if ($setRequest->userId === $setRequest->fromUserId) {
            return;
        }

        $user = $this->userRepository->getById($setRequest->userId);

        if ($user === null) {
            return;
        }

        $this->friendRepository->setFriend($setRequest->fromUserId, $setRequest->userId);

        $this->friendCreated($setRequest);
    }

    public function deleteUserFriend(DeleteRequest $deleteRequest)
    {
        if ($deleteRequest->userId === $deleteRequest->fromUserId) {
            return;
        }

        $user = $this->userRepository->getById($deleteRequest->userId);

        if ($user === null) {
            return;
        }

        $this->friendRepository->deleteFriend($deleteRequest->fromUserId, $deleteRequest->userId);

        $this->friendDeleted($deleteRequest);
    }

    public function getUserSubscribersIds(User $user): array
    {
        return $this->friendRepository->getUserSourcesByTarget($user->getId());
    }

    private function friendCreated(SetRequest $setRequest): void
    {
        $event = new FriendCreatedEvent($setRequest->fromUserId, $setRequest->userId);
        $this->eventDispatcher->dispatch($event);
    }

    private function friendDeleted(DeleteRequest $deleteRequest): void
    {
        $event = new FriendDeletedEvent($deleteRequest->fromUserId, $deleteRequest->userId);
        $this->eventDispatcher->dispatch($event);
    }
}