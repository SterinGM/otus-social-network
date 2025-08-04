<?php

namespace App\Event\Friend;

use Symfony\Contracts\EventDispatcher\Event;

class FriendCreatedEvent extends Event
{
    public readonly string $userId;
    public readonly string $friendId;

    /**
     * @param string $userId
     * @param string $friendId
     */
    public function __construct(string $userId, string $friendId)
    {
        $this->userId = $userId;
        $this->friendId = $friendId;
    }
}