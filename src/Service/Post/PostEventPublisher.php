<?php

namespace App\Service\Post;

use App\Entity\Main\Post;
use App\Message\Post\PostCreatedMessage;
use App\Service\Friend\FriendInterface;
use Symfony\Component\Messenger\Bridge\Amqp\Transport\AmqpStamp;
use Symfony\Component\Messenger\MessageBusInterface;

class PostEventPublisher
{
    private MessageBusInterface $messageBus;
    private FriendInterface $friendService;

    public function __construct(MessageBusInterface $messageBus, FriendInterface $friendService)
    {
        $this->messageBus = $messageBus;
        $this->friendService = $friendService;
    }

    public function publishPostCreated(Post $post): void
    {
        $author = $post->getAuthor();
        $friendIds = $this->friendService->getUserSubscribersIds($author);

        if (count($friendIds) == 0) {
            return;
        }

        $message = new PostCreatedMessage(
            $post->getId(),
            $post->getText(),
            $author->getId(),
            $friendIds,
            $post->getCreatedAt()
        );

        $routingKey = $message->getRoutingKey();
        $amqpStamp = new AmqpStamp($routingKey);

        $this->messageBus->dispatch($message, [$amqpStamp]);
    }
}