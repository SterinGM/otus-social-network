<?php

namespace App\WebSocket\Handler;

use Ratchet\ConnectionInterface;
use App\WebSocket\Storage\WebSocketStorage;
use InvalidArgumentException;

class SubscriptionHandler
{
    private WebSocketStorage $storage;

    public function __construct(WebSocketStorage $storage)
    {
        $this->storage = $storage;
    }

    public function handleSubscribe(ConnectionInterface $conn, array $data): void
    {
        $this->validate($data, $conn);

        $this->storage->subscribeUserToChannel($conn->userId, $data['channel']);

        $conn->send(json_encode([
            'type' => 'subscribe',
            'success' => true,
            'user_id' => $conn->userId,
            'channel' => $data['channel'],
            'timestamp' => time()
        ]));
    }

    public function handleUnsubscribe(ConnectionInterface $conn, array $data): void
    {
        $this->validate($data, $conn);

        $this->storage->unsubscribeUserFromChannel($conn->userId, $data['channel']);

        $conn->send(json_encode([
            'type' => 'unsubscribe',
            'success' => true,
            'user_id' => $conn->userId,
            'channel' => $data['channel'],
            'timestamp' => time()
        ]));
    }

    public function validate(array $data, ConnectionInterface $conn): void
    {
        if (!isset($data['channel'])) {
            throw new InvalidArgumentException('Channel name required');
        }

        if (!isset($conn->userId)) {
            throw new InvalidArgumentException('Authenticated required');
        }
    }
}