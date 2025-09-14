<?php

namespace App\Service\Friend;

use App\DTO\Friend\Request\DeleteRequest;
use App\DTO\Friend\Request\SetRequest;
use App\Entity\Main\User;

interface FriendInterface
{
    public function setUserFriend(SetRequest $setRequest): void;

    public function deleteUserFriend(DeleteRequest $deleteRequest);

  /**
   * @return string[]
   */
    public function getUserSubscribersIds(User $user): array;
}