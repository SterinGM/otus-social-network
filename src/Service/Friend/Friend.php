<?php

namespace App\Service\Friend;

use App\DTO\Friend\Request\SetRequest;
use App\Repository\UserRepository;

class Friend implements FriendInterface
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
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
    }
}