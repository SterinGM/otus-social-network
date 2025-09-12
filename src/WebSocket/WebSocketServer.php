<?php

namespace App\WebSocket;

use App\WebSocket\Storage\WebSocketStorage;
use App\WebSocket\Handler\ConnectionHandler;
use App\WebSocket\Handler\MessageHandler;
use Exception;
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use Psr\Log\LoggerInterface;

class WebSocketServer implements MessageComponentInterface
{
    private WebSocketStorage $storage;
    private ConnectionHandler $connectionHandler;
    private MessageHandler $messageHandler;
    private LoggerInterface $logger;

    public function __construct(
        WebSocketStorage  $storage,
        ConnectionHandler $connectionHandler,
        MessageHandler    $messageHandler,
        LoggerInterface   $logger
    ) {
        $this->storage = $storage;
        $this->connectionHandler = $connectionHandler;
        $this->messageHandler = $messageHandler;
        $this->logger = $logger;
    }

    public function onOpen(ConnectionInterface $conn): void
    {
        $this->connectionHandler->handleOpen($conn);
    }

    public function onClose(ConnectionInterface $conn): void
    {
        $this->connectionHandler->handleClose($conn);
    }

    public function onError(ConnectionInterface $conn, Exception $e): void
    {
        $this->connectionHandler->handleError($conn, $e);
    }

    public function onMessage(ConnectionInterface $from, $msg): void
    {
        $this->messageHandler->handleMessage($from, $msg);
    }

    public function notifyUsers(array $userIds, array $messageData, string $channel): int
    {
        $notifiedCount = 0;

        foreach ($userIds as $userId) {
            if ($this->storage->isUserSubscribedToChannel($userId, $channel)) {
                $connections = $this->storage->getUserConnections($userId);

                if ($connections !== null) {
                    foreach ($connections as $connection) {
                        try {
                            $message = [
                                'operationId' => 'postFeedPosted',
                                'message' => $messageData,
                            ];

                            $connection->send(json_encode($message));
                            $notifiedCount++;
                        } catch (Exception $e) {
                            $this->logger->error(sprintf('Не удалось отправить пользователю %d: %s', $userId, $e->getMessage()));
                        }
                    }
                }
            }
        }

        $this->logger->info(sprintf("Уведомлено %s соединений о новом посте", $notifiedCount));

        return $notifiedCount;
    }
}