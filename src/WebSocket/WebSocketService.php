<?php

namespace App\WebSocket;

use App\Message\Post\PostCreatedMessage;

class WebSocketService
{
    private WebSocketServer $webSocketServer;

    public function __construct(WebSocketServer $webSocketServer)
    {
        $this->webSocketServer = $webSocketServer;
    }

    public function notifyUsersAboutPost(PostCreatedMessage $message): int
    {
        return $this->webSocketServer->notifyUsers($message->friendIds, [
            'postId' => $message->postId,
            'postText' => $message->postText,
            'author_user_id' => $message->authorId,
        ]);
    }
}