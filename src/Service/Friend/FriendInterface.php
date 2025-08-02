<?php

namespace App\Service\Friend;

use App\DTO\Friend\Request\SetRequest;

interface FriendInterface
{
    public function setUserFriend(SetRequest $setRequest): void;
}