<?php

namespace App\DTO\Dialog;

use App\Entity\Dialog\Message as DoctrineMessage;

class Message
{
    public string $from;
    public string $to;
    public string $text;

    private function __construct(string $from, string $to, string $text)
    {
        $this->from = $from;
        $this->to = $to;
        $this->text = $text;
    }

    public static function createFromMessage(DoctrineMessage $message): self
    {
        $userIds = $message->getChat()->getUserIds();
        $fromUserId = $message->getUserId();

        if (count($userIds) === 1) {
            $toUserId = $fromUserId;
        } else {
            $key = array_search($fromUserId, $userIds);
            unset($userIds[$key]);
            $toUserId = array_values($userIds)[0];
        }

        return new self(
            $fromUserId,
            $toUserId,
            $message->getContent()
        );
    }
}