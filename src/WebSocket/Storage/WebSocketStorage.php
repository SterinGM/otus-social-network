<?php

namespace App\WebSocket\Storage;

use Ratchet\ConnectionInterface;
use SplObjectStorage;

class WebSocketStorage
{
    private readonly SplObjectStorage $clients;
    private array $userConnections;
    private array $channels;

    public function __construct()
    {
        $this->clients = new SplObjectStorage();
        $this->userConnections = [];
        $this->channels = [];
    }

    public function addClient(ConnectionInterface $conn): void
    {
        $this->clients->attach($conn);
    }

    public function removeClient(ConnectionInterface $conn): void
    {
        $this->clients->detach($conn);
    }

    public function getClients(): SplObjectStorage
    {
        return $this->clients;
    }

    public function addUserConnection(string $userId, ConnectionInterface $conn): void
    {
        if (!isset($this->userConnections[$userId])) {
            $this->userConnections[$userId] = [];
        }

        $this->userConnections[$userId][$conn->resourceId] = $conn;
    }

    public function removeUserConnection(string $userId, string $connectionId): void
    {
        if (isset($this->userConnections[$userId][$connectionId])) {
            unset($this->userConnections[$userId][$connectionId]);

            if (empty($this->userConnections[$userId])) {
                unset($this->userConnections[$userId]);
            }
        }
    }

    public function getUserConnections(string $userId): ?array
    {
        return $this->userConnections[$userId] ?? null;
    }

    public function subscribeUserToChannel(string $userId, string $channel): void
    {
        if (!isset($this->channels[$channel])) {
            $this->channels[$channel] = [];
        }

        $this->channels[$channel][$userId] = true;
    }

    public function unsubscribeUserFromChannel(string $userId, string $channel): void
    {
        if (isset($this->channels[$channel][$userId])) {
            unset($this->channels[$channel][$userId]);

            if (empty($this->channels[$channel])) {
                unset($this->channels[$channel]);
            }
        }
    }

    public function isUserSubscribedToChannel(string $userId, string $channel): bool
    {
        return isset($this->channels[$channel][$userId]);
    }
}