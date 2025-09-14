<?php

namespace App\Service\Post;

use App\DTO\Post\Request\FeedRequest;
use App\Entity\Main\Post;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

class FeedCache implements FeedInterface
{
    private const int CACHE_LIMIT = 1_000;
    private const string CACHE_PREFIX = 'feed.';
    private const string CACHE_TAG_FEED = 'feed.tag.feed-';
    private const string CACHE_TAG_POST = 'feed.tag.post-';

    private TagAwareCacheInterface $cache;
    private FeedInterface $feed;

    public function __construct(TagAwareCacheInterface $cache, FeedInterface $feed)
    {
        $this->cache = $cache;
        $this->feed = $feed;
    }

    public function get(FeedRequest $feedRequest): array
    {
        if ($feedRequest->offset >= self::CACHE_LIMIT) {
            return $this->feed->get($feedRequest);
        }

        $cacheKey = $this->getCacheKey($feedRequest);

        return $this->cache->get($cacheKey, function (ItemInterface $item) use ($feedRequest) {
            $data = $this->feed->get($feedRequest);

            $tags = $this->getCachePostTags($data);
            $tags[] = $this->getCacheFeedTag($feedRequest->userId);

            $item->tag($tags);

            return $data;
        });
    }

    /**
     * @param string[] $userIds
     */
    public function dropCacheByUsers(array $userIds): void
    {
        $tags = $this->getCacheFeedTags($userIds);

        $this->cache->invalidateTags($tags);
    }

    public function dropCacheByPost(string $postId): void
    {
        $tag = $this->getCachePostTag($postId);

        $this->cache->invalidateTags([$tag]);
    }

    private function getCacheKey(FeedRequest $feedRequest): string
    {
        return self::CACHE_PREFIX . $feedRequest->userId . '_' . $feedRequest->limit . '_' . $feedRequest->offset;
    }

    /**
     * @param string[] $userIds
     * @return string[]
     */
    private function getCacheFeedTags(array $userIds): array
    {
        $tags = [];

        foreach ($userIds as $userId) {
            $tags[] = $this->getCacheFeedTag($userId);
        }

        return $tags;
    }

    private function getCacheFeedTag(string $userId): string
    {
        return self::CACHE_TAG_FEED . $userId;
    }

    /**
     * @param Post[] $posts
     * @return string[]
     */
    private function getCachePostTags(array $posts): array
    {
        $tags = [];

        foreach ($posts as $post) {
            $tags[] = $this->getCachePostTag($post->getId());
        }

        return $tags;
    }

    private function getCachePostTag(string $postId): string
    {
        return self::CACHE_TAG_POST . $postId;
    }
}