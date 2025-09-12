<?php

namespace App\WebSocket\Handler;

use Exception;
use Ratchet\ConnectionInterface;
use App\WebSocket\Storage\WebSocketStorage;
use Psr\Log\LoggerInterface;

class ConnectionHandler
{
    private WebSocketStorage $storage;
    private LoggerInterface $logger;

    public function __construct(WebSocketStorage $storage, LoggerInterface $logger)
    {
        $this->storage = $storage;
        $this->logger = $logger;
    }

    public function handleOpen(ConnectionInterface $conn): void
    {
        $this->storage->addClient($conn);
        $this->logger->info(sprintf('Новое подключение: %s', $conn->resourceId));

        $conn->send(json_encode([
            'type' => 'connected',
            'message' => 'Добро пожаловать на WebSocket сервер',
            'timestamp' => time()
        ]));
    }

    public function handleClose(ConnectionInterface $conn): void
    {
        $this->storage->removeClient($conn);

        if (isset($conn->userId)) {
            $this->storage->removeUserConnection($conn->userId, $conn->resourceId);
        }

        $this->logger->info(sprintf('Подключение %s закрыто', $conn->resourceId));
    }

    public function handleError(ConnectionInterface $conn, Exception $e): void
    {
        $this->logger->error(sprintf('Ошибка WebSocket: %s', $e->getMessage()));
        $conn->close();
    }
}