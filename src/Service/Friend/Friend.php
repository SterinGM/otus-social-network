<?php

namespace App\Service\Friend;

use App\DTO\Friend\Request\SetRequest;
use App\Repository\FriendRepository;
use App\Repository\UserRepository;

class Friend implements FriendInterface
{
    private UserRepository $userRepository;
    private FriendRepository $friendRepository;

    public function __construct(UserRepository $userRepository, FriendRepository $friendRepository)
    {
        $this->userRepository = $userRepository;
        $this->friendRepository = $friendRepository;
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

        $this->friendRepository->setFriends($setRequest->fromUserId, $setRequest->userId);
    }
}