<?php

namespace App\EventListener;

use App\Event\Friend\FriendCreatedEvent;
use App\Event\Friend\FriendDeletedEvent;
use App\Service\Post\FeedCache;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class EventSubscriber implements EventSubscriberInterface
{
    private FeedCache $feedCache;

    public function __construct(FeedCache $feedCache)
    {
        $this->feedCache = $feedCache;
    }

    public static function getSubscribedEvents()
    {
        return [
            FriendCreatedEvent::class => 'onFriendCreated',
            FriendDeletedEvent::class => 'onFriendDeleted',
        ];
    }

    public function onFriendCreated(FriendCreatedEvent $event): void
    {
        $this->feedCache->dropCacheByUser($event->userId);
    }

    public function onFriendDeleted(FriendDeletedEvent $event): void
    {
        $this->feedCache->dropCacheByUser($event->userId);
    }
}