<?php

namespace App\WebSocket\Handler;

use Ratchet\ConnectionInterface;
use App\WebSocket\Storage\WebSocketStorage;
use App\Service\Security\ApiTokenHandler;
use Psr\Log\LoggerInterface;
use InvalidArgumentException;

class AuthenticationHandler
{
    private WebSocketStorage $storage;
    private ApiTokenHandler $apiTokenHandler;
    private LoggerInterface $logger;

    public function __construct(
        WebSocketStorage $storage,
        ApiTokenHandler $apiTokenHandler,
        LoggerInterface $logger
    ) {
        $this->storage = $storage;
        $this->apiTokenHandler = $apiTokenHandler;
        $this->logger = $logger;
    }

    public function handleAuthentication(ConnectionInterface $conn, array $data): void
    {
        if (!isset($data['user_token'])) {
            throw new InvalidArgumentException('Требуется токен авторизации');
        }

        $userId = $this->apiTokenHandler->getUserBadgeFrom($data['user_token'])->getUserIdentifier();
        $conn->userId = $userId;

        $this->storage->addUserConnection($userId, $conn);

        $conn->send(json_encode([
            'type' => 'authenticated',
            'success' => true,
            'user_id' => $userId,
            'timestamp' => time()
        ]));

        $this->logger->info(sprintf('Пользователь %s аутентифицирован через соединение %s', $userId, $conn->resourceId));
    }
}