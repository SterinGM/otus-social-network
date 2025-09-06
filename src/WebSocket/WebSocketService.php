<?php

namespace App\WebSocket;

class WebSocketService
{
    private WebSocketServer $webSocketServer;
    private bool $isServerRunning = false;

    public function __construct(WebSocketServer $webSocketServer)
    {
        $this->webSocketServer = $webSocketServer;
    }

    public function notifyUsersAboutPost(array $userIds, array $postData): int
    {
        return $this->webSocketServer->notifyUsers($userIds, [
            'post_id' => $postData['id'],
            'author_id' => $postData['author_id'],
            'content' => $postData['content'],
            'author' => $postData['author'] ?? 'Unknown',
            'created_at' => $postData['created_at'] ?? date('Y-m-d H:i:s')
        ]);
    }

    public function getStats(): array
    {
        return $this->webSocketServer->getStats();
    }

    public function broadcastMessage(array $userIds, array $message): int
    {
        return $this->webSocketServer->notifyUsers($userIds, $message);
    }

    public function isServerRunning(): bool
    {
        return $this->isServerRunning;
    }

    public function getConnectedUsers(): array
    {
        $stats = $this->getStats();

        return $stats['users'] ?? [];
    }

    public function getTotalConnections(): int
    {
        $stats = $this->getStats();

        return $stats['total_connections'] ?? 0;
    }
}