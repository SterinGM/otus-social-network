<?php

namespace App\WebSocket\Handler;

use Ratchet\ConnectionInterface;
use App\WebSocket\Storage\WebSocketStorage;
use InvalidArgumentException;
use Exception;

class MessageHandler
{
    private WebSocketStorage $storage;
    private AuthenticationHandler $authenticationHandler;
    private SubscriptionHandler $subscriptionHandler;

    public function __construct(
        WebSocketStorage      $storage,
        AuthenticationHandler $authenticationHandler,
        SubscriptionHandler   $subscriptionHandler
    ) {
        $this->storage = $storage;
        $this->authenticationHandler = $authenticationHandler;
        $this->subscriptionHandler = $subscriptionHandler;
    }

    public function handleMessage(ConnectionInterface $conn, string $message): void
    {
        try {
            $data = json_decode($message, true);

            if (!$data || !isset($data['type'])) {
                throw new InvalidArgumentException('Invalid message format');
            }

            $this->handleMessageType($conn, $data);
        } catch (Exception $e) {
            $this->sendError($conn, $e->getMessage());
        }
    }

    private function handleMessageType(ConnectionInterface $conn, array $data): void
    {
        switch ($data['type']) {
            case 'authenticate':
                $this->authenticationHandler->handleAuthentication($conn, $data);
                break;
            case 'subscribe':
                $this->subscriptionHandler->handleSubscribe($conn, $data);
                break;
            case 'unsubscribe':
                $this->subscriptionHandler->handleUnsubscribe($conn, $data);
                break;
            case 'ping':
                $this->handlePing($conn);
                break;
            case 'custom_message':
                $this->handleCustomMessage($data);
                break;
            default:
                $this->sendError($conn, 'Unknown message type');
        }
    }

    private function handlePing(ConnectionInterface $from): void
    {
        $from->send(json_encode([
            'type' => 'pong',
            'timestamp' => time()
        ]));
    }

    private function handleCustomMessage(array $data): void
    {
        $content = $data['content'] ?? '';

        foreach ($this->storage->getClients() as $client) {
            $client->send(json_encode([
                'type' => 'message',
                'message' => $content,
                'timestamp' => time()
            ]));
        }
    }

    private function sendError(ConnectionInterface $conn, string $message): void
    {
        $conn->send(json_encode([
            'type' => 'error',
            'message' => $message
        ]));
    }
}