<?php

namespace App\WebSocket\Handler;

use App\Service\ApiToken\ApiTokenProviderInterface;
use Ratchet\ConnectionInterface;
use App\WebSocket\Storage\WebSocketStorage;
use Psr\Log\LoggerInterface;
use InvalidArgumentException;

class AuthenticationHandler
{
    private WebSocketStorage $storage;
    private ApiTokenProviderInterface $apiTokenProvider;
    private LoggerInterface $logger;

    public function __construct(
        WebSocketStorage $storage,
        ApiTokenProviderInterface $apiTokenProvider,
        LoggerInterface $logger
    ) {
        $this->storage = $storage;
        $this->apiTokenProvider = $apiTokenProvider;
        $this->logger = $logger;
    }

    public function handleAuthentication(ConnectionInterface $conn, array $data): void
    {
        if (!isset($data['user_token'])) {
            throw new InvalidArgumentException('Требуется токен авторизации');
        }

        $userId = $this->apiTokenProvider->getToken($data['user_token'])->userId;
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