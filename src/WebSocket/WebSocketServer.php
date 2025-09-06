<?php

namespace App\WebSocket;

use Exception;
use InvalidArgumentException;
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use Psr\Log\LoggerInterface;
use SplObjectStorage;

class WebSocketServer implements MessageComponentInterface
{
    private SplObjectStorage $clients;
    private array $userConnections;
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->clients = new SplObjectStorage();
        $this->userConnections = [];
        $this->logger = $logger;
    }

    public function onOpen(ConnectionInterface $conn): void
    {
        $this->clients->attach($conn);
        $this->logger->info("New connection: {$conn->resourceId}");

        $conn->send(json_encode([
            'type' => 'connected',
            'message' => 'Welcome to Post WebSocket Server',
            'timestamp' => time()
        ]));
    }

    public function onMessage(ConnectionInterface $from, $msg): void
    {
        try {
            $data = json_decode($msg, true);

            if (!$data || !isset($data['type'])) {
                throw new InvalidArgumentException('Invalid message format');
            }

            switch ($data['type']) {
                case 'authenticate':
                    $this->handleAuthentication($from, $data);
                    break;

                case 'ping':
                    $from->send(json_encode([
                        'type' => 'pong',
                        'timestamp' => time()
                    ]));
                    break;

                default:
                    $from->send(json_encode([
                        'type' => 'error',
                        'message' => 'Unknown message type'
                    ]));
            }
        } catch (Exception $e) {
            $from->send(json_encode([
                'type' => 'error',
                'message' => $e->getMessage()
            ]));
        }
    }

    public function onClose(ConnectionInterface $conn): void
    {
        $this->clients->detach($conn);

        if (isset($conn->userId)) {
            unset($this->userConnections[$conn->userId][$conn->resourceId]);

            if (empty($this->userConnections[$conn->userId])) {
                unset($this->userConnections[$conn->userId]);
            }
        }

        $this->logger->info("Connection {$conn->resourceId} closed");
    }

    public function onError(ConnectionInterface $conn, Exception $e): void
    {
        $this->logger->error("WebSocket error: {$e->getMessage()}");

        $conn->close();
    }

    public function notifyUsers(array $userIds, array $messageData): int
    {
        $notifiedCount = 0;

        foreach ($userIds as $userId) {
            if (isset($this->userConnections[$userId])) {
                foreach ($this->userConnections[$userId] as $connection) {
                    try {
                        $message = [
                            'type' => 'new_post',
                            'data' => $messageData,
                            'timestamp' => time()
                        ];

                        $connection->send(json_encode($message));
                        $notifiedCount++;
                    } catch (Exception $e) {
                        $this->logger->error("Failed to send to user {$userId}: {$e->getMessage()}");
                    }
                }
            }
        }

        $this->logger->info("Notified {$notifiedCount} connections about new post");

        return $notifiedCount;
    }

    public function getStats(): array
    {
        $totalUsers = count($this->userConnections);
        $totalConnections = 0;

        foreach ($this->userConnections as $connections) {
            $totalConnections += count($connections);
        }

        return [
            'total_connections' => $this->clients->count(),
            'authenticated_users' => $totalUsers,
            'total_user_connections' => $totalConnections,
            'users' => array_keys($this->userConnections)
        ];
    }

    private function handleAuthentication(ConnectionInterface $conn, array $data): void
    {
        if (!isset($data['user_id'])) {
            throw new InvalidArgumentException('User ID required');
        }

        $userId = (int) $data['user_id'];
        $conn->userId = $userId;

        if (!isset($this->userConnections[$userId])) {
            $this->userConnections[$userId] = [];
        }

        $this->userConnections[$userId][$conn->resourceId] = $conn;

        $conn->send(json_encode([
            'type' => 'authenticated',
            'success' => true,
            'user_id' => $userId,
            'timestamp' => time()
        ]));

        $this->logger->info("User {$userId} authenticated via connection {$conn->resourceId}");
    }
}