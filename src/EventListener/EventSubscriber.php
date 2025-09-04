<?php

namespace App\EventListener;

use App\Event\Friend\FriendCreatedEvent;
use App\Event\Friend\FriendDeletedEvent;
use App\Event\Post\PostCreatedEvent;
use App\Event\Post\PostDeletedEvent;
use App\Event\Post\PostUpdatedEvent;
use App\Service\Friend\FriendInterface;
use App\Service\Post\FeedCache;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class EventSubscriber implements EventSubscriberInterface
{
    private FeedCache $feedCache;
    private FriendInterface $friendService;

    public function __construct(FeedCache $feedCache, FriendInterface $friendService)
    {
        $this->feedCache = $feedCache;
        $this->friendService = $friendService;
    }

    public static function getSubscribedEvents()
    {
        return [
            FriendCreatedEvent::class => 'onFriendCreated',
            FriendDeletedEvent::class => 'onFriendDeleted',
            PostCreatedEvent::class => 'onPostCreated',
            PostUpdatedEvent::class => 'onPostUpdated',
            PostDeletedEvent::class => 'onPostDeleted',
        ];
    }

    public function onFriendCreated(FriendCreatedEvent $event): void
    {
        $this->feedCache->dropCacheByUsers([$event->userId]);
    }

    public function onFriendDeleted(FriendDeletedEvent $event): void
    {
        $this->feedCache->dropCacheByUsers([$event->userId]);
    }

    public function onPostCreated(PostCreatedEvent $event): void
    {
        $user_ids = $this->friendService->getUserSubscribersIds($event->post->getAuthor());

        $this->feedCache->dropCacheByUsers($user_ids);
    }

    public function onPostUpdated(PostUpdatedEvent $event): void
    {
        $this->feedCache->dropCacheByPost($event->post->getId());
    }

    public function onPostDeleted(PostDeletedEvent $event): void
    {
        $user_ids = $this->friendService->getUserSubscribersIds($event->post->getAuthor());

        $this->feedCache->dropCacheByUsers($user_ids);
    }
}