<?php

namespace App\WebSocket\Consumer;

use App\Message\Post\PostCreatedMessage;
use App\WebSocket\WebSocketServer;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Bridge\Amqp\Transport\AmqpReceiver;
use Symfony\Component\Messenger\Bridge\Amqp\Transport\Connection;
use Exception;

class PostConsumer
{
    private WebSocketServer $webSocketServer;
    private LoggerInterface $logger;
    private AmqpReceiver $receiver;

    private const string POSTS_QUEUE = 'user_posts_queue';
    private const string POSTS_FEED_CHANNEL = '/post/feed/posted';

    public function __construct(
        WebSocketServer $webSocketServer,
        LoggerInterface $logger,
        string $dsn = ''
    ) {
        $this->webSocketServer = $webSocketServer;
        $this->logger = $logger;
        $this->receiver = new AmqpReceiver(Connection::fromDsn($dsn));
    }

    public function processMessages(): void
    {
        try {
            $envelopes = $this->receiver->getFromQueues([self::POSTS_QUEUE]);

            foreach ($envelopes as $envelope) {
                $message = $envelope->getMessage();

                if ($message instanceof PostCreatedMessage) {
                    $data = [
                        'postId' => $message->postId,
                        'postText' => $message->postText,
                        'author_user_id' => $message->authorId,
                    ];

                    $this->webSocketServer->notifyUsers(
                        $message->friendIds,
                        $data,
                        self::POSTS_FEED_CHANNEL
                    );

                    $this->receiver->ack($envelope);
                } else {
                    $this->receiver->reject($envelope);
                }
            }
        } catch (Exception $e) {
            $this->logger->error(sprintf('Ошибка при обработке сообщений AMQP: %s', $e->getMessage()));
        }
    }
}