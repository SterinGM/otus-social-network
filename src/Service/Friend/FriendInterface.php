<?php

namespace App\Service\Friend;

use App\DTO\Friend\Request\DeleteRequest;
use App\DTO\Friend\Request\SetRequest;

interface FriendInterface
{
    public function setUserFriend(SetRequest $setRequest): void;

    public function deleteUserFriend(DeleteRequest $deleteRequest);
}